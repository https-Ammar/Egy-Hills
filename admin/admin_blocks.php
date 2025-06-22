<?php
include 'db.php';
session_start();

$username = $_SESSION['username'] ?? 'unknown';

// ------------ إضافة بلوك ------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';

    // معالجة الصورة
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_path = 'uploads/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            // أضف البلوك
            $stmt = $conn->prepare("INSERT INTO info_blocks (title, text, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $text, $image_name);
            if ($stmt->execute()) {
                // سجل الإضافة
                $block_id = $stmt->insert_id;
                $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('add', 'info_blocks', ?, ?, NOW())");
                $log_stmt->bind_param("is", $block_id, $username);
                $log_stmt->execute();
                $log_stmt->close();
                echo "Block added and logged.<br>";
            } else {
                echo "Error adding block: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Image is required.";
    }
}

// ------------ حذف بلوك ------------
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $block_id = intval($_GET['delete_id']);

    // جلب اسم الصورة قبل الحذف
    $stmt = $conn->prepare("SELECT image FROM info_blocks WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $stmt->bind_result($image_name);
    $stmt->fetch();
    $stmt->close();

    // احذف البلوك
    $stmt = $conn->prepare("DELETE FROM info_blocks WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    if ($stmt->execute()) {
        // حذف الصورة من السيرفر
        if ($image_name && file_exists('uploads/' . $image_name)) {
            unlink('uploads/' . $image_name);
        }

        // سجل الحذف
        $log_stmt = $conn->prepare("INSERT INTO logs (action, table_name, record_id, username, created_at) VALUES ('delete', 'info_blocks', ?, ?, NOW())");
        $log_stmt->bind_param("is", $block_id, $username);
        $log_stmt->execute();
        $log_stmt->close();

        echo "Block deleted and logged.<br>";
    } else {
        echo "Error deleting block: " . $stmt->error;
    }
    $stmt->close();
}

// ------------ جلب كل البلوكات ------------
$blocks = $conn->query("SELECT * FROM info_blocks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Info Blocks</title>
</head>

<body>
    <h2>Add New Block</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Text:</label><br>
        <textarea name="text" rows="5" required></textarea><br><br>

        <label>Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Add Block</button>
    </form>

    <hr>

    <h2>All Blocks</h2>
    <?php if ($blocks->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Text</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $blocks->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['text']) ?></td>
                    <td><img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100"></td>
                    <td>
                        <a href="?delete_id=<?= $row['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this block?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No blocks found.</p>
    <?php endif; ?>
</body>

</html>