<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

function uploadFile($file)
{
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'video/mp4'];
        if (!in_array($file['type'], $allowedTypes)) {
            die("Unsupported file type: " . htmlspecialchars($file['type']));
        }
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target = __DIR__ . '/../uploads/' . $name;

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
    <style>
        .alert-auto-close {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: fadeOut 5s forwards;
            animation-delay: 2s;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php if ($message): ?>
        <div class="alert alert-success alert-auto-close"><?= htmlspecialchars($message) ?></div>
        <script>
            setTimeout(function () {
                document.querySelector('.alert-auto-close').style.display = 'none';
            }, 3000); // 3 ثواني ثم تختفي
        </script>
    <?php endif; ?>

    <div class="page-content">
        <div class="container-xxl">
            <form method="post" enctype="multipart/form-data" class="mt-5">
                <div class="row">
                    <div class="col-12">



                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label for="product-name-1" class="form-label">Project Title</label>
                                        <input type="text" name="title" id="product-name-1" class="form-control"
                                            placeholder="Project Title">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="product-name-2" class="form-label">Location</label>
                                        <input type="text" name="location" id="product-name-2" class="form-control"
                                            placeholder="Project Location">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Specifications</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-area" class="form-label">Area</label>
                                        <input type="number" name="area" id="product-area" class="form-control"
                                            placeholder="Area in sqm">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-beds" class="form-label">Number of Rooms</label>
                                        <input type="number" name="beds" id="product-beds" class="form-control"
                                            placeholder="e.g. 3">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-baths" class="form-label">Number of Baths</label>
                                        <input type="number" name="baths" id="product-baths" class="form-control"
                                            placeholder="e.g. 2">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-size" class="form-label">Size (sqm)</label>
                                        <input type="number" name="size" id="product-size" class="form-control"
                                            placeholder="e.g. 120">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-price-input" class="form-label">Price</label>
                                        <input type="number" name="price" id="product-price-input" class="form-control"
                                            placeholder="e.g. 100000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Media Files</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Cover Image</label>
                                        <input type="file" name="cover_image" accept="image/*" required
                                            class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Main Media (Image or Video)</label>
                                        <input type="file" name="main_media" accept="image/*,video/mp4"
                                            class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Multiple Images</label>
                                        <input type="file" name="multi_images[]" multiple accept="image/*"
                                            class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Additional Image (SVG or Icon)</label>
                                        <input type="file" name="svg_file" accept="image/*,image/svg+xml"
                                            class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Final Image</label>
                                        <input type="file" name="last_image" accept="image/*" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Content Sections</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Intro Title</label>
                                        <input type="text" name="intro_title" class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Intro Description</label>
                                        <textarea name="intro_text" rows="3" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Details (one point per line)</label>
                                        <textarea name="list_text" rows="3" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Additional Section Title</label>
                                        <input type="text" name="section_title" class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Additional Section Text</label>
                                        <textarea name="section_text" rows="3" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Final Section Title</label>
                                        <input type="text" name="last_title" class="form-control">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Final Section Text</label>
                                        <textarea name="last_text" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Additional Data Table</h4>
                            </div>
                            <div class="card-body">
                                <div class="col-12 mb-3">

                                    <div id="table-container" class="row g-2"></div>
                                    <button type="button" class="btn btn btn-primary  mb-3 mt-3" onclick="addRow()">Add
                                        Row</button>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Pricing Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-price" class="form-label">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text fs-20"><i class="bx bx-dollar"></i></span>
                                            <input type="number" id="product-price" class="form-control"
                                                placeholder="000">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-discount" class="form-label">Discount</label>
                                        <div class="input-group">
                                            <span class="input-group-text fs-20"><i class="bx bxs-discount"></i></span>
                                            <input type="number" id="product-discount" class="form-control"
                                                placeholder="000">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="product-tax" class="form-label">Tax</label>
                                        <div class="input-group">
                                            <span class="input-group-text fs-20"><i class="bx bxs-file-txt"></i></span>
                                            <input type="number" id="product-tax" class="form-control"
                                                placeholder="000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light mb-3 rounded">
                            <div class="row justify-content-end g-2">
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-outline-secondary w-100">Create
                                        Project</button>
                                </div>
                                <div class="col-lg-2">
                                    <button type="reset" class="btn btn-primary w-100">Cancel</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>

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
        setTimeout(function () {
            document.querySelector('.alert-auto-close').style.display = 'none';
        }, 3000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>