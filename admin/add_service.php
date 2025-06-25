<?php
include 'db.php';

// حذف الخدمة
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // جلب اسم الصورة
    $result = $conn->query("SELECT image FROM new_services WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $imagePath = __DIR__ . "/uploads/" . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // حذف البيانات
    $conn->query("DELETE FROM new_services WHERE id = $id");
    // لا يوجد إعادة توجيه
}

// إضافة خدمة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link = $_POST['link'];

    $image = $_FILES['image']['name'];
    $target = __DIR__ . "/uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO new_services (type, title, description, link, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $type, $title, $description, $link, $image);
    $stmt->execute();

    // لا يوجد إعادة توجيه
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Dashboard | Larkon - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
</head>

<body>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center gap-1">
            <h4 class="card-title flex-grow-1">Main Slider</h4>
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal-12">Add
                Product</a>
        </div>
        <div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover table-centered">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th style="width: 20px;">
                                <div class="form-check ms-1">
                                    <input type="checkbox" class="form-check-input" id="customCheck12">
                                    <label class="form-check-label" for="customCheck12"></label>
                                </div>
                            </th>
                            <th>Product Name &amp; Size</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM new_services ORDER BY created_at DESC");
                        while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="check-<?= $row['id']; ?>">
                                        <label class="form-check-label" for="check-<?= $row['id']; ?>">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                            style="background-image: url('uploads/<?= htmlspecialchars($row['image']); ?>'); background-size: cover;">
                                        </div>
                                        <div>
                                            <a href="#!"
                                                class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']); ?></a>
                                            <p class="text-muted mb-0 mt-1 fs-13">
                                                <span>Type: </span><?= htmlspecialchars($row['type']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>$1000</td>
                                <td>
                                    <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span></p>
                                    <p class="mb-0 text-muted">-</p>
                                </td>
                                <td>Real Estate</td>
                                <td>
                                    <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                        <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>4.5
                                    </span>
                                    0 Reviews
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-soft-danger btn-sm" href="?delete=<?= $row['id']; ?>"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                class="align-middle fs-18"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal-12" tabindex="-1" aria-labelledby="addProductModalLabel-12"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product - Slider 12</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <label>Type:</label>
                        <select name="type" required>
                            <option value="announcement">Announcement</option>
                            <option value="service">Service</option>
                        </select><br><br>
                        <label>Title:</label>
                        <input type="text" name="title" required><br><br>
                        <label>Description:</label>
                        <textarea name="description" required></textarea><br><br>
                        <label>Link:</label>
                        <input type="text" name="link"><br><br>
                        <label>Image:</label>
                        <input type="file" name="image" required><br><br>
                        <button type="submit">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>

</body>

</html>