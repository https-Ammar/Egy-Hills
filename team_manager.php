<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function uploadFile($file)
{
    if (!empty($file['name'])) {
        $name = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' . $name);
        return $name;
    }
    return '';
}

function deleteImage($path)
{
    if (file_exists($path)) {
        unlink($path);
    }
}

function redirectWithSuccess($page, $param)
{
    header("Location: $page?$param=1");
    exit();
}

// ----------- POST REQUESTS -----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add Team Card
    if (isset($_POST['add_team_card'])) {
        $image = uploadFile($_FILES['image']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $conn->query("INSERT INTO about_team_cards (image, name, phone) VALUES ('$image', '$name', '$phone')")
            ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_team_card')
            : exit("Error: " . $conn->error);
    }

    // Update Team Card
    if (isset($_POST['update_team_card'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $image = uploadFile($_FILES['image']);
        if ($image) {
            $old = $conn->query("SELECT image FROM about_team_cards WHERE id=$id")->fetch_assoc();
            deleteImage('uploads/' . $old['image']);
            $conn->query("UPDATE about_team_cards SET image='$image', name='$name', phone='$phone' WHERE id=$id")
                ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_team_card')
                : exit("Error: " . $conn->error);
        } else {
            $conn->query("UPDATE about_team_cards SET name='$name', phone='$phone' WHERE id=$id")
                ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_team_card')
                : exit("Error: " . $conn->error);
        }
    }

    // Save Director Card
    if (isset($_POST['save_director_card'])) {
        $image = uploadFile($_FILES['image']);
        $title = $conn->real_escape_string($_POST['title']);
        $text = $conn->real_escape_string($_POST['text']);
        $exists = $conn->query("SELECT id FROM about_director_card LIMIT 1");

        if ($exists->num_rows > 0) {
            $id = $exists->fetch_assoc()['id'];
            if ($image) {
                $old = $conn->query("SELECT image FROM about_director_card WHERE id=$id")->fetch_assoc();
                deleteImage('uploads/' . $old['image']);
                $conn->query("UPDATE about_director_card SET image='$image', title='$title', text='$text' WHERE id=$id")
                    ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_director_card')
                    : exit("Error: " . $conn->error);
            } else {
                $conn->query("UPDATE about_director_card SET title='$title', text='$text' WHERE id=$id")
                    ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_director_card')
                    : exit("Error: " . $conn->error);
            }
        } else {
            $conn->query("INSERT INTO about_director_card (image, title, text) VALUES ('$image', '$title', '$text')")
                ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_director_card')
                : exit("Error: " . $conn->error);
        }
    }

    // Add About Slider
    if (isset($_POST['add_about_slider'])) {
        $image = uploadFile($_FILES['image']);
        $conn->query("INSERT INTO about_slider (image) VALUES ('$image')")
            ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_about_slider')
            : exit("Error: " . $conn->error);
    }

    // Add Initiative
    if (isset($_POST['add_initiative'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $name = $conn->real_escape_string($_POST['name']);
        $link = $conn->real_escape_string($_POST['link']);
        $image = uploadFile($_FILES['image']);
        $conn->query("INSERT INTO about_initiatives (title, name, link, image) VALUES ('$title', '$name', '$link', '$image')")
            ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_initiative')
            : exit("Error: " . $conn->error);
    }

    // Update Initiative
    if (isset($_POST['update_initiative'])) {
        $id = intval($_POST['id']);
        $title = $conn->real_escape_string($_POST['title']);
        $name = $conn->real_escape_string($_POST['name']);
        $link = $conn->real_escape_string($_POST['link']);
        $image = uploadFile($_FILES['image']);
        if ($image) {
            $old = $conn->query("SELECT image FROM about_initiatives WHERE id=$id")->fetch_assoc();
            deleteImage('uploads/' . $old['image']);
            $conn->query("UPDATE about_initiatives SET title='$title', name='$name', link='$link', image='$image' WHERE id=$id")
                ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_initiative')
                : exit("Error: " . $conn->error);
        } else {
            $conn->query("UPDATE about_initiatives SET title='$title', name='$name', link='$link' WHERE id=$id")
                ? redirectWithSuccess($_SERVER['PHP_SELF'], 'update_initiative')
                : exit("Error: " . $conn->error);
        }
    }
}

// ----------- DELETE REQUESTS -----------
function deleteRecordWithImage($table, $idField, $id)
{
    global $conn;
    $result = $conn->query("SELECT image FROM $table WHERE $idField=$id");
    if ($result && $row = $result->fetch_assoc()) {
        deleteImage('uploads/' . $row['image']);
    }
    $conn->query("DELETE FROM $table WHERE $idField=$id");
}

// Delete Team Card
if (isset($_GET['delete_team_card'])) {
    deleteRecordWithImage('about_team_cards', 'id', intval($_GET['delete_team_card']));
    redirectWithSuccess($_SERVER['PHP_SELF'], 'delete_team_card');
}

// Delete Director Card
if (isset($_GET['delete_director_card'])) {
    deleteRecordWithImage('about_director_card', 'id', intval($_GET['delete_director_card']));
    redirectWithSuccess($_SERVER['PHP_SELF'], 'delete_director_card');
}

// Delete Slider
if (isset($_GET['delete_about_slider'])) {
    deleteRecordWithImage('about_slider', 'id', intval($_GET['delete_about_slider']));
    redirectWithSuccess($_SERVER['PHP_SELF'], 'delete_about_slider');
}

// Delete Initiative
if (isset($_GET['delete_initiative'])) {
    deleteRecordWithImage('about_initiatives', 'id', intval($_GET['delete_initiative']));
    redirectWithSuccess($_SERVER['PHP_SELF'], 'delete_initiative');
}

// ----------- FETCH DATA -----------
$about_team_cards = $conn->query("SELECT * FROM about_team_cards");
$about_director_card = $conn->query("SELECT * FROM about_director_card LIMIT 1");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$initiatives = $conn->query("SELECT * FROM about_initiatives");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Content Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <h1 class="mb-4 text-center">Dashboard - Content Manager</h1>

        <!-- Add Team Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Add New Team Card</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_team_card" class="btn btn-success">Add Card</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Team Cards List -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">Team Cards</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $about_team_cards->fetch_assoc()): ?>
                            <tr>
                                <td><img src="uploads/<?= $row['image'] ?>" width="80"></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td>
                                    <form method="POST" enctype="multipart/form-data" class="row g-1">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="col">
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                        <div class="col">
                                            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"
                                                class="form-control" required>
                                        </div>
                                        <div class="col">
                                            <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>"
                                                class="form-control">
                                        </div>
                                        <div class="col">
                                            <button type="submit" name="update_team_card"
                                                class="btn btn-warning btn-sm">Update</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <a href="?delete_team_card=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Director Card -->
        <?php $director = $about_director_card->fetch_assoc(); ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">Director Card</div>
            <div class="card-body">
                <?php if ($director && $director['image']): ?>
                    <img src="uploads/<?= $director['image'] ?>" width="120" class="mb-3 d-block">
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" name="title"
                            value="<?= $director ? htmlspecialchars($director['title']) : '' ?>" class="form-control"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Text</label>
                        <textarea name="text" rows="2" class="form-control"
                            required><?= $director ? htmlspecialchars($director['text']) : '' ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_director_card" class="btn btn-primary">Save</button>
                        <?php if ($director): ?>
                            <a href="?delete_director_card=<?= $director['id'] ?>" class="btn btn-danger ms-2"
                                onclick="return confirm('Are you sure?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- About Slider -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Add New Slider</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_about_slider" class="btn btn-success">Add Slider</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Slider List</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($slider = $about_sliders->fetch_assoc()): ?>
                            <tr>
                                <td><img src="uploads/<?= $slider['image'] ?>" width="120"></td>
                                <td>
                                    <a href="?delete_about_slider=<?= $slider['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="container my-5">
        <h3>Add New CSR Initiative</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Link (Optional)</label>
                <input type="url" name="link" class="form-control">
            </div>

            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" name="add_initiative" class="btn btn-primary">Add Initiative</button>
        </form>
    </div>
    <?php
    while ($row = $initiatives->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><a href="<?= htmlspecialchars($row['link']) ?>" target="_blank"><?= htmlspecialchars($row['link']) ?></a>
            </td>
            <td><img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="60"></td>
            <td>
                <a href="?edit_initiative=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?delete_initiative=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>

</body>

</html>