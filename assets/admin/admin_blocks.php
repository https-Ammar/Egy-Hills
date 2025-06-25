<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = '';
$error = '';

// حذف بلوك
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $block_id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("SELECT image FROM info_blocks WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $stmt->bind_result($image_name);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM info_blocks WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    if ($stmt->execute()) {
        if ($image_name && file_exists("uploads/$image_name")) {
            unlink("uploads/$image_name");
        }

        $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('delete', 'info_blocks', ?, ?, NOW())");
        $log_stmt->bind_param("is", $block_id, $username);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=deleted");
        exit();
    } else {
        $error = "Error deleting block: " . $stmt->error;
    }
    $stmt->close();
}

// إضافة بلوك
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $user = $_POST['username'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_name = '';

    $uploads_dir = __DIR__ . '/uploads';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "$uploads_dir/$image_name");
    }

    if ($image_name) {
        $stmt = $conn->prepare("INSERT INTO info_blocks (image, phone, username, amount, payment_method, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdds", $image_name, $phone, $user, $amount, $payment_method, $description);

        if ($stmt->execute()) {
            $block_id = $stmt->insert_id;

            $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('add', 'info_blocks', ?, ?, NOW())");
            $log_stmt->bind_param("is", $block_id, $username);
            $log_stmt->execute();
            $log_stmt->close();

            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=added");
            exit();
        } else {
            $error = "Error inserting block: " . $stmt->error;
        }
    } else {
        $error = "Main image is required.";
    }
}

$blocks = $conn->query("SELECT * FROM info_blocks ORDER BY id DESC");

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = "Block added successfully.";
    }
    if ($_GET['msg'] === 'deleted') {
        $message = "Block deleted successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Info Blocks
    </title>
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
</head>

<body>


    <div class="container mt-5">

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Info Blocks</h4>
                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBlockModal">Add
                    Block</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-centered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Phone</th>
                            <th>Username</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($blocks->num_rows > 0): ?>
                            <?php while ($row = $blocks->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>


                                    <td>
                                        <?php if ($row['image'] && file_exists("uploads/{$row['image']}")): ?>


                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url(uploads/<?= $row['image'] ?>">
                                            </div>
                                        <?php endif; ?>

                                    </td>

                                    </td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['amount']) ?></td>
                                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                    <td>
                                        <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this block?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No blocks found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal لإضافة بلوك -->
        <div class="modal fade" id="addBlockModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Block</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input name="payment_method" class="form-control mb-2" placeholder="Payment Method">
                            <input name="username" class="form-control mb-2" placeholder="Username">
                            <input name="phone" class="form-control mb-2" placeholder="Phone Number">
                            <input name="amount" type="number" step="0.01" class="form-control mb-2"
                                placeholder="Amount">
                            <textarea name="description" class="form-control mb-2"
                                placeholder="Description (optional)"></textarea>
                            <label class="form-label mt-2">Main Image:</label>
                            <input type="file" name="image" accept="image/*" class="form-control mb-2" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Block</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>