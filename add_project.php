<?php
include 'db.php';

// دالة رفع الملفات مع دعم الصور والفيديوهات
function uploadFile($file)
{
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'video/mp4'
        ];
        if (!in_array($file['type'], $allowedTypes)) {
            die("نوع الملف غير مسموح: " . htmlspecialchars($file['type']));
        }
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target = 'uploads/' . $name;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $name;
        } else {
            die("فشل رفع الملف.");
        }
    }
    return null;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // رفع الملفات
    $cover_image = uploadFile($_FILES['cover_image']);
    $main_media = uploadFile($_FILES['main_media']);
    $extra_image = uploadFile($_FILES['svg_file']);
    $last_image = uploadFile($_FILES['last_image']);

    // البيانات النصية
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $area = trim($_POST['area'] ?? '');
    $beds = intval($_POST['beds'] ?? 0);
    $baths = intval($_POST['baths'] ?? 0);
    $size = trim($_POST['size'] ?? '0');
    $price = trim($_POST['price'] ?? '');

    $subtitle = trim($_POST['intro_title'] ?? '');
    $description = trim($_POST['intro_text'] ?? '');
    $details = trim($_POST['list_text'] ?? '');

    $extra_title = trim($_POST['section_title'] ?? '');
    $extra_text = trim($_POST['section_text'] ?? '');

    $last_title = trim($_POST['last_title'] ?? '');
    $last_text = trim($_POST['last_text'] ?? '');

    $video_url = ''; // حقل الفيديو URL إن أردت لاحقًا

    // صور متعددة
    $multi_images = [];
    if (!empty($_FILES['multi_images']['name'][0])) {
        foreach ($_FILES['multi_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['multi_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file_array = [
                    'name' => $_FILES['multi_images']['name'][$key],
                    'type' => $_FILES['multi_images']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['multi_images']['error'][$key],
                    'size' => $_FILES['multi_images']['size'][$key]
                ];
                $img_name = uploadFile($file_array);
                $multi_images[] = $img_name;
            }
        }
    }

    // صفوف الجدول الفرعي
    $table_rows = $_POST['table_rows'] ?? [];

    // إضافة المشروع الرئيسي
    $stmt = $conn->prepare("
        INSERT INTO projects 
        (image, main_media, location, title, price, beds, baths, size, area, video_url, subtitle, description, details, extra_title, extra_text, extra_image, last_title, last_text, last_image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssiissssssssssss",
        $cover_image,
        $main_media,
        $location,
        $title,
        $price,
        $beds,
        $baths,
        $size,
        $area,
        $video_url,
        $subtitle,
        $description,
        $details,
        $extra_title,
        $extra_text,
        $extra_image,
        $last_title,
        $last_text,
        $last_image
    );

    if ($stmt->execute()) {
        $project_id = $stmt->insert_id;
        $stmt->close();

        // صور متعددة
        foreach ($multi_images as $img) {
            $stmt_img = $conn->prepare("INSERT INTO project_images (project_id, image) VALUES (?, ?)");
            $stmt_img->bind_param("is", $project_id, $img);
            $stmt_img->execute();
            $stmt_img->close();
        }

        // صفوف الجدول الفرعي
        foreach ($table_rows as $row) {
            $col1 = trim($row[0] ?? '');
            $col2 = trim($row[1] ?? '');
            $stmt_table = $conn->prepare("INSERT INTO project_table (project_id, col1, col2) VALUES (?, ?, ?)");
            $stmt_table->bind_param("iss", $project_id, $col1, $col2);
            $stmt_table->execute();
            $stmt_table->close();
        }

        $message = "✅ تم إضافة المشروع بنجاح!";
    } else {
        $message = "❌ حدث خطأ: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إضافة مشروع</title>
    <style>
        body {
            max-width: 800px;
            margin: auto;
            font-family: Arial;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input,
        textarea,
        button {
            padding: 8px;
            font-size: 16px;
        }

        .message {
            color: green;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <h1>إضافة مشروع متكامل</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <label>صورة كافر</label>
        <input type="file" name="cover_image" accept="image/*" required>

        <label>فيديو أو صورة رئيسية</label>
        <input type="file" name="main_media" accept="image/*,video/mp4">

        <label>عنوان المشروع</label>
        <input type="text" name="title" value="">

        <label>المكان</label>
        <input type="text" name="location" value="">

        <label>المساحة</label>
        <input type="text" name="area" value="">

        <label>عدد الغرف</label>
        <input type="number" name="beds" value="0">

        <label>عدد الحمامات</label>
        <input type="number" name="baths" value="0">

        <label>المساحة بالمتر</label>
        <input type="text" name="size" value="">

        <label>السعر</label>
        <input type="text" name="price" value="">

        <label>عنوان تعريفي</label>
        <input type="text" name="intro_title" value="">

        <label>وصف</label>
        <textarea name="intro_text"></textarea>

        <label>تفاصيل (سطر لكل نقطة)</label>
        <textarea name="list_text"></textarea>

        <label>عنوان إضافي</label>
        <input type="text" name="section_title" value="">

        <label>نص إضافي</label>
        <textarea name="section_text"></textarea>

        <label>صورة إضافية (SVG أو أيقونة)</label>
        <input type="file" name="svg_file" accept="image/*,image/svg+xml">

        <label>صور متعددة</label>
        <input type="file" name="multi_images[]" multiple accept="image/*">

        <label>عنوان أخير</label>
        <input type="text" name="last_title" value="">

        <label>نص أخير</label>
        <textarea name="last_text"></textarea>

        <label>صورة أخيرة</label>
        <input type="file" name="last_image" accept="image/*">

        <div>
            <h3>إضافة صفوف جدول</h3>
            <button type="button" onclick="addRow()">إضافة صف</button>
            <div id="table-container"></div>
        </div>

        <button type="submit">إضافة المشروع</button>
    </form>


    
    <script>
        function addRow() {
            const container = document.getElementById('table-container');
            const row = document.createElement('div');
            for (let i = 0; i < 2; i++) {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `table_rows[${container.childElementCount}][]`;
                input.placeholder = 'بيان';
                row.appendChild(input);
            }
            container.appendChild(row);
        }
    </script>
</body>

</html>








