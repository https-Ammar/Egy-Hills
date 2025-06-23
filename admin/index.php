<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$stmt->close();

$visits_result = $conn->query("SELECT COUNT(*) AS total FROM site_visits");
$total_visits = 0;
if ($visits_result && $row = $visits_result->fetch_assoc()) {
    $total_visits = $row['total'];
}

function uploadFile($file)
{
    if (!empty($file['name'])) {
        $name = time() . '_' . basename($file['name']);
        $uploadDir = '/Applications/MAMP/htdocs/Egy-Hills/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destination = $uploadDir . $name;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $name;
        }
    }
    return '';
}

function redirectWithSuccess($page, $param)
{
    header("Location: $page?$param=1");
    exit();
}

function logAction($conn, $action, $table_name)
{
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $action, $table_name);
    $stmt->execute();
    $stmt->close();
}

function logPlanAndRoom($conn, $plan_id, $image, $title, $description, $action, $user)
{
    $stmt = $conn->prepare("INSERT INTO plan_and_room_logs (plan_id, image, title, description, action, user, date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssss", $plan_id, $image, $title, $description, $action, $user);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT id, image, title, location, price FROM projects");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slider'])) {
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO sliders (image) VALUES ('$image')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_slider') : exit("Error inserting slider: " . $conn->error);
}
if (isset($_GET['delete_slider'])) {
    $conn->query("DELETE FROM sliders WHERE id=" . intval($_GET['delete_slider']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_about_card'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $link = $conn->real_escape_string($_POST['link']);
    $conn->query("INSERT INTO about_cards (image, title, description, link) VALUES ('$image', '$title', '$desc', '$link')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_about_card') : exit("Error inserting about card: " . $conn->error);
}
if (isset($_GET['delete_about_card'])) {
    $conn->query("DELETE FROM about_cards WHERE id=" . intval($_GET['delete_about_card']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_highlight'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO highlights (image, title, description) VALUES ('$image', '$title', '$desc')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_highlight') : exit("Error inserting highlight: " . $conn->error);
}
if (isset($_GET['delete_highlight'])) {
    $conn->query("DELETE FROM highlights WHERE id=" . intval($_GET['delete_highlight']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("INSERT INTO videos (url) VALUES ('$url')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_video') : exit("Error inserting video: " . $conn->error);
}
if (isset($_GET['delete_video'])) {
    $conn->query("DELETE FROM videos WHERE id=" . intval($_GET['delete_video']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO ads (image, title, description) VALUES ('$image', '$title', '$desc')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_ad') : exit("Error inserting ad: " . $conn->error);
}
if (isset($_GET['delete_ad'])) {
    $conn->query("DELETE FROM ads WHERE id=" . intval($_GET['delete_ad']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad_icon'])) {
    $ad_id = intval($_POST['ad_id']);
    $icon = uploadFile($_FILES['icon']);
    $title = $conn->real_escape_string($_POST['title']);
    $text = $conn->real_escape_string($_POST['text']);
    $conn->query("INSERT INTO ad_icons (ad_id, icon, title, text) VALUES ($ad_id, '$icon', '$title', '$text')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_ad_icon') : exit("Error inserting ad icon: " . $conn->error);
}
if (isset($_GET['delete_ad_icon'])) {
    $conn->query("DELETE FROM ad_icons WHERE id=" . intval($_GET['delete_ad_icon']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question = $conn->real_escape_string($_POST['question']);
    $answer = $conn->real_escape_string($_POST['answer']);
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO questions (question, answer, image) VALUES ('$question', '$answer', '$image')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_question') : exit("Error inserting question: " . $conn->error);
}
if (isset($_GET['delete_question'])) {
    $conn->query("DELETE FROM questions WHERE id=" . intval($_GET['delete_question']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $icon = uploadFile($_FILES['icon']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO services (icon, title, description) VALUES ('$icon', '$title', '$desc')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_service') : exit("Error inserting service: " . $conn->error);
}
if (isset($_GET['delete_service'])) {
    $conn->query("DELETE FROM services WHERE id=" . intval($_GET['delete_service']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property_highlight'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("INSERT INTO property_highlights (image, title) VALUES ('$image', '$title')") ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_property_highlight') : exit("Error inserting property highlight: " . $conn->error);
}
if (isset($_GET['delete_property_highlight'])) {
    $conn->query("DELETE FROM property_highlights WHERE id=" . intval($_GET['delete_property_highlight']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan_and_room'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    if ($conn->query("INSERT INTO plan_and_room (image, title, description) VALUES ('$image', '$title', '$description')")) {
        $last_id = $conn->insert_id;
        redirectWithSuccess($_SERVER['PHP_SELF'], 'add_plan_and_room');
    } else {
        exit("Error inserting plan and room: " . $conn->error);
    }
}
if (isset($_GET['delete_plan_and_room'])) {
    $id = intval($_GET['delete_plan_and_room']);
    $result = $conn->query("SELECT * FROM plan_and_room WHERE id=$id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image = $row['image'];
        $title = $row['title'];
        $description = $row['description'];
        $user = 'Admin';
        $conn->query("DELETE FROM plan_and_room WHERE id=$id");
        logPlanAndRoom($conn, $id, $image, $title, $description, 'delete', $user);
        redirectWithSuccess($_SERVER['PHP_SELF'], 'delete_plan_and_room');
    }
}

$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$ad_icons = $conn->query("SELECT * FROM ad_icons");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");
$plan_and_room = $conn->query("SELECT * FROM plan_and_room");
$property_highlights = $conn->query("SELECT * FROM property_highlights");
$visitors = $conn->query("SELECT id, name, phone, created_at FROM visitors ORDER BY id DESC");
$logs = $conn->query("SELECT * FROM logs ORDER BY created_at DESC");
$plan_and_room_logs = $conn->query("SELECT * FROM plan_and_room_logs ORDER BY date DESC");
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">

</head>

<body>

    <main class="contint">
        <nav>
            <div class="logo-name">
                <div class="logo-image">
                    <img src="images/logo.png" alt="">
                </div>
                <span class="logo_name">Egy - - Hills</span>
            </div>
            <div class="menu-items">
                <ul class="nav-links">
                    <li>
                        <a href="#" class="xyxbtn123" data-id="box1">
                            <i class="uil uil-estate"></i>
                            <span class="link-name">Dashboard</span>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="#" class="xyxbtn123" data-id="box2">Main Slider</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box3">About Cards</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box4">Property Highlights</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box5">Features list</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box6">Videos</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box7">Advertisement</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box9">Plan Room</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box8">Why Choose Us</a></li>
                            <li><a href="#" class="xyxbtn123" data-id="box10">Services Card</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="./add_project.php" data-target="comment">
                            <i class="uil uil-comments"></i>
                            <span class="link-name">Add Project</span>
                        </a>
                    </li>
                    <li>
                        <a href="./add_service.php" data-target="share">
                            <i class="uil uil-share"></i>
                            <span class="link-name">Add Service</span>
                        </a>
                    </li>

                    <li>
                        <a href="./admin_blocks.php" data-target="content">
                            <i class="uil uil-files-landscapes"></i>
                            <span class="link-name">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="./requests.php" data-target="analytics">
                            <i class="uil uil-chart"></i>
                            <span class="link-name">requests</span>
                        </a>
                    </li>
                    <li>
                        <a href="./manage_projects.php" data-target="like">
                            <i class="uil uil-thumbs-up"></i>
                            <span class="link-name"> projects</span>
                        </a>
                    </li>

                </ul>
                <ul class="logout-mode">
                    <li>
                        <a href="./logout.php">
                            <i class="uil uil-signout"></i>
                            <span class="link-name">Logout</span>
                        </a>
                    </li>
                    <li class="mode">
                        <a href="#">
                            <i class="uil uil-moon"></i>
                            <span class="link-name">Dark Mode</span>
                        </a>
                        <div class="mode-toggle">
                            <span class="switch"></span>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <section class="dashboard">
            <div class="top">
                <i class="uil uil-bars sidebar-toggle"></i>
                <div class="search-box">
                    <i class="uil uil-search"></i>
                    <input type="text" placeholder="Search here...">
                </div>
            </div>
            <div class="dash-content">
                <div class="overview">
                    <div class="title">
                        <i class="uil uil-tachometer-fast-alt"></i>
                        <span class="text">Dashboard</span>
                    </div>
                    <div class="boxes">
                        <div class="box box1">
                            <i class="uil uil-thumbs-up"></i>
                            <span class="text">Total Likes</span>


                            <span class="number"> <?= $total_visits ?></span>

                        </div>
                        <div class="box box2">
                            <i class="uil uil-comments"></i>
                            <span class="text">Comments</span>
                            <span class="number" id="visitor-count"></span>

                        </div>


                        <div class="box box3">
                            <i class="uil uil-share"></i>
                            <span class="text">Total Share</span>
                            <span class="number" id="project-count">10,120</span>


                            <script>
                                window.addEventListener('DOMContentLoaded', () => {
                                    const rows = document.querySelectorAll('ul.d-flex');
                                    const count = rows.length;
                                    const result = Math.max(0, 1 - count);
                                    document.getElementById("visitor-count").textContent = result;
                                });
                            </script>



                            <script>
                                window.addEventListener('DOMContentLoaded', () => {
                                    const projectItems = document.querySelectorAll('.list-group-item');
                                    const projectCount = projectItems.length;

                                    const countElement = document.getElementById('project-count');
                                    countElement.textContent = `${projectCount}`;
                                });
                            </script>


                        </div>
                    </div>
                </div>
                <div class="activity">

                    <div id="box1" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Recent Activity</span>
                        </div>

                        <div class="container my-5" id="dashboard">
                            <div class="block_">
                                <ul
                                    class="d-flex align-items-center justify-content-between fw-bold border-bottom pb-2 mb-3">
                                    <li>ID</li>
                                    <li>Name</li>
                                    <li>Phone</li>
                                    <li>Date</li>
                                    <li>Time</li>
                                </ul>

                                <?php
                                $visitors = $conn->query("SELECT id, name, phone, created_at FROM visitors ORDER BY id DESC");
                                ?>

                                <?php if ($visitors && $visitors->num_rows > 0): ?>
                                    <?php while ($visitor = $visitors->fetch_assoc()): ?>
                                        <ul class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                                            <li><?= (int) $visitor['id'] ?></li>
                                            <li><?= htmlspecialchars($visitor['name']) ?></li>
                                            <li><?= htmlspecialchars($visitor['phone']) ?></li>
                                            <li><?= date('Y-m-d', strtotime($visitor['created_at'])) ?></li>
                                            <li><?= date('H:i:s', strtotime($visitor['created_at'])) ?></li>
                                        </ul>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <ul class="text-center">
                                        <li>No user</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div id="box2" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Main Slider</span>
                        </div>

                        <div class="mb-4 d-flex flex-wrap gap-3">
                            <?php while ($row = $sliders->fetch_assoc()): ?>
                                <div class="text-center">
                                    <div class="img_admin"
                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                        <a href="?delete_slider=<?= intval($row['id']) ?>"
                                            class="btn btn-danger btn-sm mt-1">x</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <hr>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <input type="file" name="image" class="form-control" required>
                                <button name="add_slider" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                    <div id="box3" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">About Cards</span>
                        </div>

                        <div class="mb-4 d-flex flex-wrap gap-3">
                            <?php while ($row = $about_cards->fetch_assoc()): ?>
                                <div class="text-center">
                                    <div class="img_admin"
                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                        <a href="?delete_about_card=<?= intval($row['id']) ?>"
                                            class="btn btn-danger btn-sm mt-1">x</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" placeholder="Title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="description" placeholder="Description" class="form-control"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="link" placeholder="Link" class="form-control" required>
                            </div>
                            <button name="add_about_card" class="btn btn-primary">Add Card</button>
                        </form>
                    </div>
                    <div id="box4" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Property Highlights</span>
                        </div>

                        <div class="mb-4 d-flex flex-wrap gap-4">
                            <?php while ($row = $highlights->fetch_assoc()): ?>
                                <div class="text-center">
                                    <div class="img_admin"
                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                        <a href="?delete_highlight=<?= intval($row['id']) ?>"
                                            class="btn btn-danger btn-sm mt-1">x</a>
                                    </div>
                                    <span class="card-title d-block mt-2"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" placeholder="Title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="description" placeholder="Description" class="form-control"
                                    required></textarea>
                            </div>
                            <button name="add_highlight" class="btn btn-primary">Add Highlight</button>
                        </form>
                    </div>
                    <div id="box5" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Features list</span>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-5">
                            <div class="mb-3">
                                <input type="file" name="image" id="highlightImage" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" id="highlightTitle" class="form-control"
                                    placeholder="Enter Title" required>
                            </div>
                            <button type="submit" name="add_property_highlight" class="btn btn-success">Add Property
                                Highlight</button>
                        </form>

                        <section class="container my-5">
                            <?php if ($property_highlights && $property_highlights->num_rows > 0): ?>
                                <h4 class="mb-4">Property Highlights List</h4>
                                <div class="list-group">
                                    <?php while ($row = $property_highlights->fetch_assoc()): ?>
                                        <div
                                            class="list-group-item d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>" width="100"
                                                    class="img-thumbnail me-3">
                                                <h6 class="mb-0"><?= htmlspecialchars($row['title']) ?></h6>
                                            </div>
                                            <a href="?delete_property_highlight=<?= intval($row['id']) ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mt-4">🚫 No Property Highlights available yet.</div>
                            <?php endif; ?>
                        </section>
                    </div>
                    <div id="box6" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Videos</span>
                        </div>

                        <form method="POST" class="mb-3">
                            <div class="mb-3 d-flex align-items-center gap-3">
                                <input type="url" name="url" placeholder="Video URL" class="form-control" required>
                                <button name="add_video" class="btn btn-primary">Add</button>
                            </div>
                        </form>

                        <ul class="list-group mb-4">
                            <?php while ($row = $videos->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($row['url']) ?>
                                    <a href="?delete_video=<?= intval($row['id']) ?>"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <div id="box7" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">advertisement</span>
                        </div>

                        <div class="mb-4 d-flex flex-wrap gap-4">
                            <?php while ($row = $ads->fetch_assoc()): ?>
                                <div class="text-center">
                                    <div class="img_admin"
                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                        <a href="?delete_ad=<?= intval($row['id']) ?>"
                                            class="btn btn-danger btn-sm mt-1">x</a>
                                    </div>
                                    <span class="card-title d-block mt-2"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" placeholder="Title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="description" placeholder="Description" class="form-control"
                                    required></textarea>
                            </div>
                            <button name="add_ad" class="btn btn-primary">Add Ad</button>
                        </form>

                        <hr>

                        <h3>Add Icon to Ad</h3>
                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <select name="ad_id" class="form-select">
                                    <?php
                                    $ads2 = $conn->query("SELECT * FROM ads");
                                    while ($a = $ads2->fetch_assoc()) {
                                        echo "<option value='{$a['id']}'>" . htmlspecialchars($a['title']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="icon" accept="image/*,.svg" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" placeholder="Title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="text" placeholder="Text" class="form-control" required></textarea>
                            </div>
                            <button name="add_ad_icon" class="btn btn-primary">Add Icon</button>
                        </form>

                        <h3>All Ad Icons</h3>
                        <div class="d-flex flex-wrap gap-4">
                            <?php while ($icon = $ad_icons->fetch_assoc()): ?>
                                <div class="text-center">
                                    <img src="/Egy-Hills/uploads/<?= htmlspecialchars($icon['icon']) ?>" width="50"
                                        class="img-thumbnail mb-2">
                                    <div class="small fw-bold"><?= htmlspecialchars($icon['title']) ?></div>
                                    <div class="small"><?= htmlspecialchars($icon['text']) ?></div>
                                    <a href="?delete_ad_icon=<?= intval($icon['id']) ?>"
                                        class="btn btn-danger btn-sm mt-1">Delete</a>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <hr>

                        <div class="mb-4 d-flex flex-wrap gap-3">
                            <?php while ($row = $about_sliders->fetch_assoc()): ?>
                                <div class="text-center">
                                    <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>" width="100"
                                        class="img-thumbnail">
                                    <a href="?delete_about_slider=<?= intval($row['id']) ?>"
                                        class="btn btn-danger btn-sm mt-1">Delete</a>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <hr>
                    </div>
                    <div id="box8" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Why Choose Us</span>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="text" name="question" placeholder="Question" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="answer" placeholder="Answer" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control">
                            </div>
                            <button name="add_question" class="btn btn-primary">Add Q&A</button>
                        </form>

                        <ul class="list-group mb-4">
                            <?php while ($row = $questions->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($row['question']) ?>
                                    <a href="?delete_question=<?= intval($row['id']) ?>"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <div id="box9" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Add Plan and Room</span>
                        </div>

                        <section class="container my-5">
                            <form method="POST" enctype="multipart/form-data" class="mb-5">
                                <div class="mb-3">
                                    <input type="file" name="image" id="planImage" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="title" id="planTitle" class="form-control"
                                        placeholder="Enter Title" required>
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" id="planDescription" class="form-control"
                                        placeholder="Enter Description" rows="4" required></textarea>
                                </div>
                                <button type="submit" name="add_plan_and_room" class="btn btn-primary">Add Plan and
                                    Room</button>
                            </form>

                            <h4>Plan and Room List</h4>
                            <div class="table-responsive mb-5">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $plan_and_room->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>"
                                                        width="100" class="img-thumbnail"></td>
                                                <td><?= htmlspecialchars($row['title']) ?></td>
                                                <td><?= htmlspecialchars($row['description']) ?></td>
                                                <td>
                                                    <a href="?delete_plan_and_room=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this item?')">🗑️
                                                        Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Uncomment this section to enable logs
        <h4>Plan and Room Logs</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Action</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $plan_and_room_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= htmlspecialchars($log['title']) ?></td>
                            <td><?= htmlspecialchars($log['description']) ?></td>
                            <td><?= htmlspecialchars($log['user']) ?></td>
                            <td><?= htmlspecialchars($log['date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        -->
                        </section>
                    </div>
                    <div id="box10" class="ptn_box_open">
                        <div class="title">
                            <i class="uil uil-clock-three"></i>
                            <span class="text">Services Card</span>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="file" name="icon" accept="image/*,.svg" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" placeholder="Title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="description" placeholder="Description" class="form-control"
                                    required></textarea>
                            </div>
                            <button name="add_service" class="btn btn-primary">Add Service</button>
                        </form>

                        <div class="container my-4">
                            <?php while ($row = $services->fetch_assoc()): ?>
                                <div class="row align-items-center border rounded p-3 mb-3">
                                    <div class="col-md-2 text-center mb-2 mb-md-0">
                                        <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['icon']) ?>" width="60"
                                            class="img-thumbnail">
                                    </div>
                                    <div class="col-md-3">
                                        <strong><?= htmlspecialchars($row['title']) ?></strong>
                                    </div>
                                    <div class="col-md-5">
                                        <p class="mb-0"><?= htmlspecialchars($row['description']) ?></p>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <a href="?delete_service=<?= intval($row['id']) ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this item?')">🗑️
                                            Delete</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>


                    <div class="container py-4">
                        <h1 class="mb-4 text-center">إدارة المشاريع</h1>

                        <?php if ($result && $result->num_rows > 0): ?>
                            <ul class="list-group">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex align-items-center justify-content-between flex-wrap"
                                        id="card-<?= $row['id'] ?>">
                                        <div class="d-flex align-items-center flex-grow-1 gap-3">
                                            <?php if (!empty($row['image'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                                    alt="<?= htmlspecialchars($row['title']) ?>" class="img-thumbnail"
                                                    style="width: 120px; height: 80px; object-fit: cover;" />
                                            <?php else: ?>
                                                <div class="bg-secondary rounded" style="width: 120px; height: 80px;"></div>
                                            <?php endif; ?>
                                            <div class="project-info text-end flex-grow-1">
                                                <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                                                <p class="mb-1">📍 <?= htmlspecialchars($row['location']) ?></p>
                                                <p class="mb-0">💰 <?= htmlspecialchars($row['price']) ?></p>

                                                <form class="edit-form mt-2 d-none" id="edit-form-<?= $row['id'] ?>"
                                                    onsubmit="event.preventDefault(); updateProject(<?= $row['id'] ?>);">
                                                    <div class="mb-2">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="title-<?= $row['id'] ?>"
                                                            value="<?= htmlspecialchars($row['title']) ?>"
                                                            placeholder="عنوان المشروع" />
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="location-<?= $row['id'] ?>"
                                                            value="<?= htmlspecialchars($row['location']) ?>"
                                                            placeholder="الموقع" />
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="price-<?= $row['id'] ?>"
                                                            value="<?= htmlspecialchars($row['price']) ?>"
                                                            placeholder="السعر" />
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-success">💾 حفظ
                                                        التعديلات</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="btn-group btn-group-sm flex-column flex-sm-row gap-2 mt-3 mt-sm-0">
                                            <button class="btn btn-danger" onclick="deleteProject(<?= $row['id'] ?>)">🗑️
                                                حذف</button>
                                            <button class="btn btn-primary" onclick="toggleEditForm(<?= $row['id'] ?>)">✏️
                                                تعديل</button>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center">لا يوجد مشاريع مضافة حتى الآن.</p>
                        <?php endif; ?>
                        <script>
                            function deleteProject(id) {
                                if (!confirm("هل أنت متأكد من حذف هذا المشروع؟")) return;
                                fetch('delete_project.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: 'id=' + id
                                })
                                    .then(response => response.text())
                                    .then(data => {
                                        if (data.trim() === 'success') {
                                            document.getElementById('card-' + id).remove();
                                        } else {
                                            alert('❌ حدث خطأ أثناء الحذف');
                                        }
                                    });
                            }

                            function toggleEditForm(id) {
                                const form = document.getElementById('edit-form-' + id);
                                form.classList.toggle('d-none');
                            }

                            function updateProject(id) {
                                const title = document.getElementById('title-' + id).value;
                                const location = document.getElementById('location-' + id).value;
                                const price = document.getElementById('price-' + id).value;

                                fetch('update_project.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `id=${id}&title=${encodeURIComponent(title)}&location=${encodeURIComponent(location)}&price=${encodeURIComponent(price)}`
                                })
                                    .then(response => response.text())
                                    .then(data => {
                                        if (data.trim() === 'success') {
                                            alert('✅ تم حفظ التعديلات');
                                            location.reload();
                                        } else {
                                            alert('❌ حدث خطأ أثناء التعديل');
                                        }
                                    });
                            }
                        </script>

                    </div>






                </div>
            </div>
        </section>

    </main>



    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const savedId = localStorage.getItem('selectedBoxId');
            if (savedId) {
                document.querySelectorAll('.ptn_box_open').forEach(div => {
                    div.style.display = (div.id === savedId) ? 'block' : 'none';
                });
            }

            document.querySelectorAll('.xyxbtn123').forEach(btn => {
                btn.onclick = () => {
                    const id = btn.dataset.id;
                    localStorage.setItem('selectedBoxId', id);
                    document.querySelectorAll('.ptn_box_open').forEach(div => {
                        div.style.display = (div.id === id && div.style.display !== 'block') ? 'block' : 'none';
                    });
                };
            });

            const body = document.querySelector("body"),
                modeToggle = body.querySelector(".mode-toggle"),
                sidebar = body.querySelector("nav"),
                sidebarToggle = body.querySelector(".sidebar-toggle");

            let getMode = localStorage.getItem("mode");
            if (getMode === "dark") {
                body.classList.add("dark");
            }

            let getStatus = localStorage.getItem("status");
            if (getStatus === "close") {
                sidebar.classList.add("close");
            }

            modeToggle.addEventListener("click", () => {
                body.classList.toggle("dark");
                if (body.classList.contains("dark")) {
                    localStorage.setItem("mode", "dark");
                } else {
                    localStorage.setItem("mode", "light");
                }
            });

            sidebarToggle.addEventListener("click", () => {
                sidebar.classList.toggle("close");
                if (sidebar.classList.contains("close")) {
                    localStorage.setItem("status", "close");
                } else {
                    localStorage.setItem("status", "open");
                }
            });
        });
    </script>


</body>

</html>