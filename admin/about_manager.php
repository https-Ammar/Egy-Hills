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
        move_uploaded_file($file['tmp_name'], '/Applications/MAMP/htdocs/Egy-Hills/uploads/' . $name);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_team_card'])) {
        $image = uploadFile($_FILES['image']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $conn->query("INSERT INTO about_team_cards (image, name, phone) VALUES ('$image', '$name', '$phone')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['update_team_card'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $image = uploadFile($_FILES['image']);
        if ($image) {
            $old = $conn->query("SELECT image FROM about_team_cards WHERE id=$id")->fetch_assoc();
            deleteImage('/Egy-Hills/uploads/' . $old['image']);
            $conn->query("UPDATE about_team_cards SET image='$image', name='$name', phone='$phone' WHERE id=$id");
        } else {
            $conn->query("UPDATE about_team_cards SET name='$name', phone='$phone' WHERE id=$id");
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['save_director_card'])) {
        $image = uploadFile($_FILES['image']);
        $title = $conn->real_escape_string($_POST['title']);
        $text = $conn->real_escape_string($_POST['text']);
        $exists = $conn->query("SELECT id FROM about_director_card LIMIT 1");
        if ($exists->num_rows > 0) {
            $id = $exists->fetch_assoc()['id'];
            if ($image) {
                $old = $conn->query("SELECT image FROM about_director_card WHERE id=$id")->fetch_assoc();
                deleteImage('/Egy-Hills/uploads/' . $old['image']);
                $conn->query("UPDATE about_director_card SET image='$image', title='$title', text='$text' WHERE id=$id");
            } else {
                $conn->query("UPDATE about_director_card SET title='$title', text='$text' WHERE id=$id");
            }
        } else {
            $conn->query("INSERT INTO about_director_card (image, title, text) VALUES ('$image', '$title', '$text')");
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_about_slider'])) {
        $image = uploadFile($_FILES['image']);
        $conn->query("INSERT INTO about_slider (image) VALUES ('$image')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_initiative'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $name = $conn->real_escape_string($_POST['name']);
        $link = $conn->real_escape_string($_POST['link']);
        $image = uploadFile($_FILES['image']);
        $conn->query("INSERT INTO about_initiatives (title, name, link, image) VALUES ('$title', '$name', '$link', '$image')");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['update_initiative'])) {
        $id = intval($_POST['id']);
        $title = $conn->real_escape_string($_POST['title']);
        $name = $conn->real_escape_string($_POST['name']);
        $link = $conn->real_escape_string($_POST['link']);
        $image = uploadFile($_FILES['image']);
        if ($image) {
            $old = $conn->query("SELECT image FROM about_initiatives WHERE id=$id")->fetch_assoc();
            deleteImage('/Egy-Hills/uploads/' . $old['image']);
            $conn->query("UPDATE about_initiatives SET title='$title', name='$name', link='$link', image='$image' WHERE id=$id");
        } else {
            $conn->query("UPDATE about_initiatives SET title='$title', name='$name', link='$link' WHERE id=$id");
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['delete_team_card'])) {
        $id = intval($_GET['delete_team_card']);
        $result = $conn->query("SELECT image FROM about_team_cards WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/Egy-Hills/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_team_cards WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_director_card'])) {
        $id = intval($_GET['delete_director_card']);
        $result = $conn->query("SELECT image FROM about_director_card WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/Egy-Hills/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_director_card WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_about_slider'])) {
        $id = intval($_GET['delete_about_slider']);
        $result = $conn->query("SELECT image FROM about_slider WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/Egy-Hills/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_slider WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    if (isset($_GET['delete_initiative'])) {
        $id = intval($_GET['delete_initiative']);
        $result = $conn->query("SELECT image FROM about_initiatives WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            deleteImage('/Egy-Hills/uploads/' . $row['image']);
        }
        $conn->query("DELETE FROM about_initiatives WHERE id=$id");
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

$about_team_cards = $conn->query("SELECT * FROM about_team_cards");
$about_director_card = $conn->query("SELECT * FROM about_director_card LIMIT 1");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$initiatives = $conn->query("SELECT * FROM about_initiatives");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard - Content Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <h1>Dashboard - Content Manager</h1>

    <section>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <input type="text" name="name" required>
            <input type="text" name="phone">
            <button type="submit" name="add_team_card">Add Card</button>
        </form>
    </section>

    <section>
        <?php while ($row = $about_team_cards->fetch_assoc()): ?>
            <div>
                <img src="/Egy-Hills/uploads/<?= $row['image'] ?>" width="80">
                <div>Name: <?= htmlspecialchars($row['name']) ?></div>
                <div>Phone: <?= htmlspecialchars($row['phone']) ?></div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="file" name="image">
                    <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                    <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>">
                    <button type="submit" name="update_team_card">Update</button>
                </form>
                <a href="?delete_team_card=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </section>

    <section>
        <?php $director = $about_director_card->fetch_assoc(); ?>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($director && $director['image']): ?>
                <img src="/Egy-Hills/uploads/<?= $director['image'] ?>" width="120">
            <?php endif; ?>
            <input type="file" name="image">
            <input type="text" name="title" value="<?= $director ? htmlspecialchars($director['title']) : '' ?>"
                required>
            <textarea name="text" required><?= $director ? htmlspecialchars($director['text']) : '' ?></textarea>
            <button type="submit" name="save_director_card">Save</button>
            <?php if ($director): ?>
                <a href="?delete_director_card=<?= $director['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            <?php endif; ?>
        </form>
    </section>

    <section>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <button type="submit" name="add_about_slider">Add Slider</button>
        </form>
    </section>

    <section>
        <?php while ($slider = $about_sliders->fetch_assoc()): ?>
            <div>
                <img src="/Egy-Hills/uploads/<?= $slider['image'] ?>" width="120">
                <a href="?delete_about_slider=<?= $slider['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </section>

    <section>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" required>
            <input type="text" name="name" required>
            <input type="url" name="link">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" name="add_initiative">Add Initiative</button>
        </form>
    </section>

    <section>
        <?php while ($row = $initiatives->fetch_assoc()): ?>
            <div>
                <div>Title: <?= htmlspecialchars($row['title']) ?></div>
                <div>Name: <?= htmlspecialchars($row['name']) ?></div>
                <div>Link: <a href="<?= htmlspecialchars($row['link']) ?>"
                        target="_blank"><?= htmlspecialchars($row['link']) ?></a></div>
                <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>" width="60">
                <a href="?edit_initiative=<?= $row['id'] ?>">Edit</a>
                <a href="?delete_initiative=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </section>
</body>

</html>