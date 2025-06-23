<?php
include 'db.php';

// Delete entry if requested
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM new_services WHERE id = $id");
    header("Location: addd.php");
    exit;
}

// Add new entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link = $_POST['link'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "/Applications/MAMP/htdocs/Egy-Hills/uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO new_services (type, title, description, link, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $type, $title, $description, $link, $image);
    $stmt->execute();
    header("Location: addd.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h2 class="mb-4">Add New Service or Announcement</h2>
        <form method="post" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-control" required>
                    <option value="announcement">Announcement</option>
                    <option value="service">Service</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Link</label>
                <input type="text" name="link" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Add</button>
        </form>

        <h3 class="mb-4">Manage Existing Entries</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM new_services ORDER BY created_at DESC");
                while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" width="60"></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <a href="addd.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>