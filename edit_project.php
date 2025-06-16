<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("رقم المشروع غير صالح.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM projects WHERE id = $id");
if ($result->num_rows == 0) {
    die("المشروع غير موجود.");
}

$project = $result->fetch_assoc();
$message = '';

function uploadFile($file)
{
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'video/mp4'];
        if (!in_array($file['type'], $allowedTypes)) {
            die("نوع الملف غير مسموح.");
        }
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        $name = time() . '_' . basename($file['name']);
        $target = 'uploads/' . $name;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $name;
        } else {
            die("فشل رفع الملف.");
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = $_POST['price'] ?? '';
    $beds = intval($_POST['beds'] ?? 0);
    $baths = intval($_POST['baths'] ?? 0);
    $size = $_POST['size'] ?? '';
    $area = $_POST['area'] ?? '';

    $cover_image = $project['image'];
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $cover_image = uploadFile($_FILES['cover_image']);
    }

    $stmt = $conn->prepare("
        UPDATE projects SET 
            title = ?, 
            location = ?, 
            price = ?, 
            beds = ?, 
            baths = ?, 
            size = ?, 
            area = ?, 
            image = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssissssi",
        $title,
        $location,
        $price,
        $beds,
        $baths,
        $size,
        $area,
        $cover_image,
        $id
    );

    if ($stmt->execute()) {
        $message = "✅ تم تحديث المشروع بنجاح.";
        // إعادة تحميل البيانات المحدثة
        $result = $conn->query("SELECT * FROM projects WHERE id = $id");
        $project = $result->fetch_assoc();
    } else {
        $message = "❌ حدث خطأ: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تعديل المشروع</title>
    <style>
        body {
            max-width: 600px;
            margin: auto;
            font-family: Arial;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input,
        button {
            padding: 8px;
            font-size: 16px;
        }

        .message {
            margin: 20px 0;
            color: green;
        }

        img {
            max-width: 200px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h1>تعديل المشروع</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>العنوان</label>
        <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>

        <label>المكان</label>
        <input type="text" name="location" value="<?= htmlspecialchars($project['location']) ?>" required>

        <label>السعر</label>
        <input type="text" name="price" value="<?= htmlspecialchars($project['price']) ?>" required>

        <label>عدد الغرف</label>
        <input type="number" name="beds" value="<?= (int) $project['beds'] ?>">

        <label>عدد الحمامات</label>
        <input type="number" name="baths" value="<?= (int) $project['baths'] ?>">

        <label>المساحة</label>
        <input type="text" name="size" value="<?= htmlspecialchars($project['size']) ?>">

        <label>المساحة (كود)</label>
        <input type="text" name="area" value="<?= htmlspecialchars($project['area']) ?>">

        <label>صورة الغلاف الحالية:</label><br>
        <?php if (!empty($project['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($project['image']) ?>" alt="صورة الغلاف">
        <?php else: ?>
            لا توجد صورة.
        <?php endif; ?>

        <label>تغيير صورة الغلاف:</label>
        <input type="file" name="cover_image" accept="image/*">

        <button type="submit">تحديث</button>
    </form>
</body>

</html>