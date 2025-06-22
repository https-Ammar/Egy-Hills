<?php
include 'db.php';

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
            die("Unsupported file type: " . htmlspecialchars($file['type']));
        }
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target = 'uploads/' . $name;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $name;
        } else {
            die("File upload failed.");
        }
    }
    return null;
}

$message = '';
if (isset($_GET['success'])) {
    $message = "✅ Project added successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_image = uploadFile($_FILES['cover_image']);
    $main_media = uploadFile($_FILES['main_media']);
    $extra_image = uploadFile($_FILES['svg_file']);
    $last_image = uploadFile($_FILES['last_image']);

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
    $video_url = '';

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

    $table_rows = $_POST['table_rows'] ?? [];

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

        foreach ($multi_images as $img) {
            $stmt_img = $conn->prepare("INSERT INTO project_images (project_id, image) VALUES (?, ?)");
            $stmt_img->bind_param("is", $project_id, $img);
            $stmt_img->execute();
            $stmt_img->close();
        }

        foreach ($table_rows as $row) {
            $col1 = trim($row[0] ?? '');
            $col2 = trim($row[1] ?? '');
            $stmt_table = $conn->prepare("INSERT INTO project_table (project_id, col1, col2) VALUES (?, ?, ?)");
            $stmt_table->bind_param("iss", $project_id, $col1, $col2);
            $stmt_table->execute();
            $stmt_table->close();
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Add Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-5">
    <h1 class="mb-4">Add New Project</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-12">
            <label class="form-label">Cover Image</label>
            <input type="file" name="cover_image" accept="image/*" required class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Main Media (Image or Video)</label>
            <input type="file" name="main_media" accept="image/*,video/mp4" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Project Title</label>
            <input type="text" name="title" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Area</label>
            <input type="text" name="area" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Number of Rooms</label>
            <input type="number" name="beds" value="0" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Number of Bathrooms</label>
            <input type="number" name="baths" value="0" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Size (sqm)</label>
            <input type="text" name="size" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Price</label>
            <input type="text" name="price" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Intro Title</label>
            <input type="text" name="intro_title" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Intro Description</label>
            <textarea name="intro_text" rows="3" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Details (one point per line)</label>
            <textarea name="list_text" rows="3" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Additional Section Title</label>
            <input type="text" name="section_title" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Additional Section Text</label>
            <textarea name="section_text" rows="3" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Additional Image (SVG or Icon)</label>
            <input type="file" name="svg_file" accept="image/*,image/svg+xml" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Multiple Images</label>
            <input type="file" name="multi_images[]" multiple accept="image/*" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Final Section Title</label>
            <input type="text" name="last_title" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Final Section Text</label>
            <textarea name="last_text" rows="3" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Final Image</label>
            <input type="file" name="last_image" accept="image/*" class="form-control">
        </div>
        <div class="col-12">
            <h5 class="mt-4">Add Table Rows</h5>
            <button type="button" class="btn btn-secondary mb-3" onclick="addRow()">Add Row</button>
            <div id="table-container" class="row g-2"></div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Add Project</button>
        </div>
    </form>

    <script>
        function addRow() {
            const container = document.getElementById('table-container');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2';
            for (let i = 0; i < 2; i++) {
                const col = document.createElement('div');
                col.className = 'col';
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `table_rows[${container.childElementCount}][]`;
                input.placeholder = 'Value';
                input.className = 'form-control';
                col.appendChild(input);
                row.appendChild(col);
            }
            container.appendChild(row);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>