<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $result = $conn->query("SELECT image FROM new_services WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $imagePath = __DIR__ . "/uploads/" . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $conn->query("DELETE FROM new_services WHERE id = $id");
}

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">add service</h4>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addProductModal-12">Add
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
                                <th> &amp; Product </th>
                                <th>Name</th>
                                <th>tect</th>
                                <th>Category</th>
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
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                            style="background-image: url('uploads/<?= htmlspecialchars($row['image']); ?>'); background-size: cover;">
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['title']); ?></td>


                                    <td><?= htmlspecialchars($row['type']); ?></td>
                                    <td>Real Estate</td>

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
                        <h5 class="modal-title">Add Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="announcement">Announcement</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="text" name="link" id="link" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="image" class="form-control" required>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>

</body>

</html>