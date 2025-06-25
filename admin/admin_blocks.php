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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = __DIR__ . '/uploads';

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_path = $uploads_dir . '/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO info_blocks (title, text, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $text, $image_name);
            if ($stmt->execute()) {
                $block_id = $stmt->insert_id;
                $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('add', 'info_blocks', ?, ?, NOW())");
                $log_stmt->bind_param("is", $block_id, $username);
                $log_stmt->execute();
                $log_stmt->close();
                $message = "Block added and logged.";
            } else {
                $error = "Error adding block: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error uploading image.";
        }
    } else {
        $error = "Image is required.";
    }
}

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
        if ($image_name && file_exists(__DIR__ . '/uploads/' . $image_name)) {
            unlink(__DIR__ . '/uploads/' . $image_name);
        }
        $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('delete', 'info_blocks', ?, ?, NOW())");
        $log_stmt->bind_param("is", $block_id, $username);
        $log_stmt->execute();
        $log_stmt->close();
        $message = "Block deleted and logged.";
    } else {
        $error = "Error deleting block: " . $stmt->error;
    }
    $stmt->close();
}

$blocks = $conn->query("SELECT * FROM info_blocks ORDER BY id DESC");
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Manage Info Blocks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Add New Block</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label for="title" class="form-label">Title:</label>
                <input id="title" type="text" name="title" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="text" class="form-label">Text:</label>
                <textarea id="text" name="text" rows="5" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image:</label>
                <input id="image" type="file" name="image" accept="image/*" class="form-control" required />
            </div>

            <button type="submit" class="btn btn-primary">Add Block</button>
        </form>

        <h2 class="mb-4">All Blocks</h2>
        <?php if ($blocks && $blocks->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Text</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $blocks->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['text'])) ?></td>
                                <td>
                                    <?php if (!empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Image" width="100"
                                            class="img-thumbnail" />
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this block?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No blocks found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>