
<?php
session_start();
include 'db.php';

// ✅ تحقق من الجلسة - يمنع الوصول إذا لم يكن المستخدم مسجل دخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// === وظيفة رفع الملفات ===
function uploadFile($file)
{
    if (!empty($file['name'])) {
        $name = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' . $name);
        return $name;
    }
    return '';
}

// === إعادة التوجيه مع رسالة نجاح ===
function redirectWithSuccess($page, $param)
{
    header("Location: $page?$param=1");
    exit();
}

// === سجل العمليات العام ===
function logAction($conn, $action, $table_name)
{
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $action, $table_name);
    $stmt->execute();
    $stmt->close();
}

// === سجل خاص بـ plan_and_room_logs ===
function logPlanAndRoom($conn, $plan_id, $image, $title, $description, $action, $user)
{
    $stmt = $conn->prepare("INSERT INTO plan_and_room_logs (plan_id, image, title, description, action, user, date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssss", $plan_id, $image, $title, $description, $action, $user);
    $stmt->execute();
    $stmt->close();
}

// === استعلامات المشاريع ===
$result = $conn->query("SELECT id, image, title, location, price FROM projects");

// === الأقسام الأخرى كما هي ===

// sliders
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slider'])) {
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO sliders (image) VALUES ('$image')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_slider')
        : exit("Error inserting slider: " . $conn->error);
}
if (isset($_GET['delete_slider'])) {
    $conn->query("DELETE FROM sliders WHERE id=" . intval($_GET['delete_slider']));
}


// about_cards
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_about_card'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $link = $conn->real_escape_string($_POST['link']);
    $conn->query("INSERT INTO about_cards (image, title, description, link) VALUES ('$image', '$title', '$desc', '$link')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_about_card')
        : exit("Error inserting about card: " . $conn->error);
}
if (isset($_GET['delete_about_card'])) {
    $conn->query("DELETE FROM about_cards WHERE id=" . intval($_GET['delete_about_card']));
}

// highlights
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_highlight'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO highlights (image, title, description) VALUES ('$image', '$title', '$desc')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_highlight')
        : exit("Error inserting highlight: " . $conn->error);
}
if (isset($_GET['delete_highlight'])) {
    $conn->query("DELETE FROM highlights WHERE id=" . intval($_GET['delete_highlight']));
}

// videos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("INSERT INTO videos (url) VALUES ('$url')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_video')
        : exit("Error inserting video: " . $conn->error);
}
if (isset($_GET['delete_video'])) {
    $conn->query("DELETE FROM videos WHERE id=" . intval($_GET['delete_video']));
}

// ads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO ads (image, title, description) VALUES ('$image', '$title', '$desc')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_ad')
        : exit("Error inserting ad: " . $conn->error);
}
if (isset($_GET['delete_ad'])) {
    $conn->query("DELETE FROM ads WHERE id=" . intval($_GET['delete_ad']));
}

// ad_icons
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad_icon'])) {
    $ad_id = intval($_POST['ad_id']);
    $icon = uploadFile($_FILES['icon']);
    $title = $conn->real_escape_string($_POST['title']);
    $text = $conn->real_escape_string($_POST['text']);
    $conn->query("INSERT INTO ad_icons (ad_id, icon, title, text) VALUES ($ad_id, '$icon', '$title', '$text')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_ad_icon')
        : exit("Error inserting ad icon: " . $conn->error);
}
if (isset($_GET['delete_ad_icon'])) {
    $conn->query("DELETE FROM ad_icons WHERE id=" . intval($_GET['delete_ad_icon']));
}

// questions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question = $conn->real_escape_string($_POST['question']);
    $answer = $conn->real_escape_string($_POST['answer']);
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO questions (question, answer, image) VALUES ('$question', '$answer', '$image')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_question')
        : exit("Error inserting question: " . $conn->error);
}
if (isset($_GET['delete_question'])) {
    $conn->query("DELETE FROM questions WHERE id=" . intval($_GET['delete_question']));
}

// services
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $icon = uploadFile($_FILES['icon']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO services (icon, title, description) VALUES ('$icon', '$title', '$desc')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_service')
        : exit("Error inserting service: " . $conn->error);
}
if (isset($_GET['delete_service'])) {
    $conn->query("DELETE FROM services WHERE id=" . intval($_GET['delete_service']));
}

// Property Highlights
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property_highlight'])) {
    $image = uploadFile($_FILES['image']);
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("INSERT INTO property_highlights (image, title) VALUES ('$image', '$title')")
        ? redirectWithSuccess($_SERVER['PHP_SELF'], 'add_property_highlight')
        : exit("Error inserting property highlight: " . $conn->error);
}
if (isset($_GET['delete_property_highlight'])) {
    $conn->query("DELETE FROM property_highlights WHERE id=" . intval($_GET['delete_property_highlight']));
}

// Plan and Room مع سجل خاص
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

// === استعلامات العرض ===
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
$visitors = $conn->query("SELECT id, name, phone FROM visitors ORDER BY id DESC");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>



    <main>
        <nav>
            <div class="logo-name">
                <div class="logo-image">
                    <img src="images/logo.png" alt="">
                </div>
                <span class="logo_name">Egy - - Hills</span>
            </div>
            <div class="menu-items">


                </head>


                <ul class="nav-links">
                    <li><a href="#" data-target="dashboard"><i class="uil uil-estate"></i> <span
                                class="link-name">Dashboard</span></a></li>
                    <li><a href="#" data-target="content"><i class="uil uil-files-landscapes"></i> <span
                                class="link-name">Content</span></a></li>
                    <li><a href="#" data-target="analytics"><i class="uil uil-chart"></i> <span
                                class="link-name">Analytics</span></a></li>
                    <li><a href="#" data-target="like"><i class="uil uil-thumbs-up"></i> <span
                                class="link-name">Like</span></a></li>
                    <li><a href="#" data-target="comment"><i class="uil uil-comments"></i> <span
                                class="link-name">Comment</span></a></li>
                    <li><a href="#" data-target="share"><i class="uil uil-share"></i> <span
                                class="link-name">Share</span></a></li>
                </ul>



                <ul class="logout-mode">
                    <li><a href="#">
                            <i class="uil uil-signout"></i>
                            <span class="link-name">Logout</span>
                        </a></li>
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

                <!--<img src="images/profile.jpg" alt="">-->
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
                            <span class="number">50,120</span>
                        </div>
                        <div class="box box2">
                            <i class="uil uil-comments"></i>
                            <span class="text">Comments</span>
                            <!-- <span class="number"><?= $booking_count ?></span> -->
                            <span class="number">10,120</span>

                        </div>
                        <div class="box box3">
                            <i class="uil uil-share"></i>
                            <span class="text">Total Share</span>
                            <span class="number">10,120</span>
                        </div>
                    </div>
                </div>




                <div class="activity">
                    <div class="title">
                        <i class="uil uil-clock-three"></i>
                        <span class="text">Recent Activity</span>
                    </div>
                    <div class="activity-data">

                        <div class="content">






                            <div class="container my-5" id="dashboard">
                                <div class="block_">
                                    <ul class="d-flex align-items-center justify-content-between">
                                        <li>ID</li>
                                        <li>Name</li>
                                        <li>Phone</li>
                                    </ul>

                                    <?php if ($visitors && $visitors->num_rows > 0): ?>
                                        <?php while ($visitor = $visitors->fetch_assoc()): ?>
                                            <ul class="d-flex align-items-center justify-content-between">
                                                <li><?php echo (int) $visitor['id']; ?></li>
                                                <li><?php echo htmlspecialchars($visitor['name']); ?></li>
                                                <li><?php echo htmlspecialchars($visitor['phone']); ?></li>
                                            </ul>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <ul>
                                            <li>no user</li>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>






                            <div id="content">
                                <div class="container py-5">

                                    <!-- Main Slider -->
                                    <h2>Main Slider</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="file" name="image" class="form-control" required>
                                        </div>
                                        <button name="add_slider" class="btn btn-primary">Add Slider</button>
                                    </form>
                                    <div class="mb-4">
                                        <?php while ($row = $sliders->fetch_assoc()): ?>
                                            <div class="d-inline-block text-center me-3">
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100"
                                                    class="img-thumbnail">

                                                <a href="?delete_slider=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm mt-1">Delete</a>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>




                                    <!-- Highlights -->
                                    <h2>Property Highlights</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="file" name="image" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="title" placeholder="Title" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="description" placeholder="Description" class="form-control"
                                                required></textarea>
                                        </div>
                                        <button name="add_highlight" class="btn btn-primary">Add Highlight</button>
                                    </form>
                                    <div class="mb-4">
                                        <?php while ($row = $highlights->fetch_assoc()): ?>
                                            <div class="card d-inline-block text-center me-3 mb-3" style="width: 150px;">
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                                    class="card-img-top">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                    <a href="?delete_highlight=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>

                                    <!-- Videos -->
                                    <h2>Videos</h2>
                                    <form method="POST" class="mb-3">
                                        <div class="mb-3">
                                            <input type="url" name="url" placeholder="Video URL" class="form-control"
                                                required>
                                        </div>
                                        <button name="add_video" class="btn btn-primary">Add Video</button>
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
                                    <hr>

                                    <!-- Ads with Section -->
                                    <h2>Ads</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="file" name="image" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="title" placeholder="Title" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="description" placeholder="Description" class="form-control"
                                                required></textarea>
                                        </div>

                                        <button name="add_ad" class="btn btn-primary">Add Ad</button>
                                    </form>
                                    <div class="mb-4">
                                        <?php while ($row = $ads->fetch_assoc()): ?>
                                            <div class="card d-inline-block text-center me-3 mb-3" style="width: 150px;">
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                                    class="card-img-top">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                    <small
                                                        class="text-muted"><?= htmlspecialchars($row['section']) ?></small>
                                                    <a href="?delete_ad=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm mt-1">Delete</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>

                                    <!-- Add Icon to Ad -->
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
                                            <input type="file" name="icon" accept="image/*,.svg" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="title" placeholder="Title" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="text" placeholder="Text" class="form-control"
                                                required></textarea>
                                        </div>
                                        <button name="add_ad_icon" class="btn btn-primary">Add Icon</button>
                                    </form>

                                    <h3>All Ad Icons</h3>
                                    <div class="mb-4">
                                        <?php while ($icon = $ad_icons->fetch_assoc()): ?>
                                            <div class="d-inline-block text-center me-3 mb-3">
                                                <img src="uploads/<?= htmlspecialchars($icon['icon']) ?>" width="50"
                                                    class="img-thumbnail">
                                                <div><?= htmlspecialchars($icon['title']) ?></div>
                                                <small><?= htmlspecialchars($icon['text']) ?></small>
                                                <a href="?delete_ad_icon=<?= intval($icon['id']) ?>"
                                                    class="btn btn-danger btn-sm mt-1">Delete Icon</a>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>
                             
                                    <div class="mb-4">
                                        <?php while ($row = $about_sliders->fetch_assoc()): ?>
                                            <div class="d-inline-block text-center me-3">
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100"
                                                    class="img-thumbnail">

                                                <a href="?delete_about_slider=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm mt-1">Delete</a>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>
                                    <h2>About Cards</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="file" name="image" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="title" placeholder="Title" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="description" placeholder="Description" class="form-control"
                                                required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="link" placeholder="Link" class="form-control"
                                                required>
                                        </div>
                                        <button name="add_about_card" class="btn btn-primary">Add Card</button>
                                    </form>
                                    <div class="mb-4">
                                        <?php while ($row = $about_cards->fetch_assoc()): ?>
                                            <div class="card d-inline-block text-center me-3 mb-3" style="width: 150px;">
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                                    class="card-img-top">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                    <a href="?delete_about_card=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>
                                    <h2>Why Choose Us</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="text" name="question" placeholder="Question"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="answer" placeholder="Answer" class="form-control"
                                                required></textarea>
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
                                    <hr>

                                    <!-- Services -->
                                    <h2>Our Real Estate Services</h2>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <input type="file" name="icon" accept="image/*,.svg" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="title" placeholder="Title" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="description" placeholder="Description" class="form-control"
                                                required></textarea>
                                        </div>
                                        <button name="add_service" class="btn btn-primary">Add Service</button>
                                    </form>
                                    <div class="mb-4">
                                        <?php while ($row = $services->fetch_assoc()): ?>
                                            <div class="card d-inline-block text-center me-3 mb-3" style="width: 150px;">
                                                <img src="uploads/<?= htmlspecialchars($row['icon']) ?>"
                                                    class="card-img-top" width="50">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                    <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                                                    <a href="?delete_service=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>



                                    <!-- Questions -->

                                    <!-- === Plan and Room Section === -->
<section class="container my-5">
  <h3 class="mb-4">Add Plan and Room</h3>
  <form method="POST" enctype="multipart/form-data" class="mb-5">
    <div class="mb-3">
      <label for="planImage" class="form-label">Upload Image</label>
      <input type="file" name="image" id="planImage" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="planTitle" class="form-label">Title</label>
      <input type="text" name="title" id="planTitle" class="form-control" placeholder="Enter Title" required>
    </div>
    <div class="mb-3">
      <label for="planDescription" class="form-label">Description</label>
      <textarea name="description" id="planDescription" class="form-control" placeholder="Enter Description" rows="4" required></textarea>
    </div>
    <button type="submit" name="add_plan_and_room" class="btn btn-primary">Add Plan and Room</button>
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
            <td><img src="uploads/<?php echo $row['image']; ?>" width="100" class="img-thumbnail"></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
              <a href="?delete_plan_and_room=<?php echo $row['id']; ?>" 
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to delete this item?')">
                 🗑️ Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

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
            <td><?php echo htmlspecialchars($log['action']); ?></td>
            <td><?php echo htmlspecialchars($log['title']); ?></td>
            <td><?php echo htmlspecialchars($log['description']); ?></td>
            <td><?php echo htmlspecialchars($log['user']); ?></td>
            <td><?php echo htmlspecialchars($log['date']); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- === Property Highlight Section === -->
<section class="container my-5">
  <h3 class="mb-4">Add Property Highlight</h3>
  <form method="POST" enctype="multipart/form-data" class="mb-5">
    <div class="mb-3">
      <label for="highlightImage" class="form-label">Upload Image</label>
      <input type="file" name="image" id="highlightImage" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="highlightTitle" class="form-label">Title</label>
      <input type="text" name="title" id="highlightTitle" class="form-control" placeholder="Enter Title" required>
    </div>
    <button type="submit" name="add_property_highlight" class="btn btn-success">Add Property Highlight</button>
  </form>

  <?php if ($property_highlights && $property_highlights->num_rows > 0): ?>
    <h4>Property Highlights List</h4>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $property_highlights->fetch_assoc()): ?>
            <tr>
              <td>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" width="100" class="img-thumbnail">
              </td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td>
                <a href="?delete_property_highlight=<?php echo $row['id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this item?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">🚫 No Property Highlights available yet.</div>
  <?php endif; ?>
</section>


                                    </section>

                                </div>


                            </div>



                 
                            <!-- ✅ قسم المشاريع بشكل مميز مع Bootstrap -->
<div id="analytics" class="container my-5">
  <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4" id="card-<?= $row['id'] ?>">
          <div class="card shadow-sm border-0">
            <?php if (!empty($row['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
              <p class="card-text mb-1">📍 <?= htmlspecialchars($row['location']) ?></p>
              <p class="card-text">💰 <?= htmlspecialchars($row['price']) ?></p>

              <div class="d-flex justify-content-between">
                <button class="btn btn-danger btn-sm" onclick="deleteProject(<?= $row['id'] ?>)">🗑️ حذف</button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#edit-<?= $row['id'] ?>">
                  ✏️ تعديل
                </button>
              </div>

              <!-- ✅ بوب أب التعديل باستخدام Collapse -->
              <div class="collapse mt-3" id="edit-<?= $row['id'] ?>">
                <form onsubmit="event.preventDefault(); updateProject(<?= $row['id'] ?>);">
                  <div class="mb-2">
                    <input type="text" class="form-control" id="title-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['title']) ?>" placeholder="عنوان المشروع">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control" id="location-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['location']) ?>" placeholder="الموقع">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control" id="price-<?= $row['id'] ?>" value="<?= htmlspecialchars($row['price']) ?>" placeholder="السعر">
                  </div>
                  <button class="btn btn-success btn-sm w-100" type="submit">💾 حفظ التعديلات</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center">لا يوجد مشاريع مضافة حتى الآن.</p>
    <?php endif; ?>
  </div>
</div>

<!-- ✅ سكربت الحذف والتعديل -->
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

                            
                            <div id="like">

                            </div>
                            <div id="comment">This is the Comment section</div>
                            <div id="share">This is the Share section</div>
                        </div>


                        <!--  -->


                    </div>

                </div>




            </div>
        </section>




        <script>
    const body = document.querySelector("body"),
        modeToggle = body.querySelector(".mode-toggle"),
        sidebar = body.querySelector("nav"),
        sidebarToggle = body.querySelector(".sidebar-toggle"),
        links = document.querySelectorAll('.nav-links a'),
        sections = document.querySelectorAll('.content > div');

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
        localStorage.setItem("mode", body.classList.contains("dark") ? "dark" : "light");
    });

    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("close");
        localStorage.setItem("status", sidebar.classList.contains("close") ? "close" : "open");
    });

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            sections.forEach(sec => sec.classList.remove('active'));
            const targetId = link.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
            localStorage.setItem('activeSection', targetId);
        });
    });

    const savedSection = localStorage.getItem('activeSection') || 'dashboard';
    document.getElementById(savedSection).classList.add('active');
</script>



    <style>
        ul.d-flex.align-items-center.justify-content-between {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
            /* text-align: center; */
            line-height: 3;
            list-style: none;
            border-bottom: 1px solid #c0c0c0;
            padding: 5px;
        }
    </style>


</body>

</html>