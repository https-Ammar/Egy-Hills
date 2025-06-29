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
$stmt = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $role);
$stmt->fetch();
if ($stmt->num_rows === 0) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['role'] = $role;
$stmt->close();

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

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

if (isAdmin()) {
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
<html lang="en" data-bs-theme="dark" data-menu-color="dark" data-bs-theme="dark ">

<head>
    <meta charset="utf-8" />
    <title>Dashboard | Larkon - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
    <style>
        .rounded.bg-light.avatar-md.d-flex.align-items-center.justify-content-center.size_ {
            background-size: 25px !important;
            background-position: center center !important;
            background-repeat: no-repeat;
        }

        .avatar-md.bg-soft-primary.rounded {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-md.bg-soft-primary.rounded svg {
            color: #ff6c30;
            width: 35px;
            height: 35px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>


</head>

<body>

    <div class="wrapper">

        <header class="topbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <div class="d-flex align-items-center">
                        <div class="topbar-item">
                            <button type="button" class="button-toggle-menu me-2">
                                <iconify-icon icon="solar:hamburger-menu-broken"
                                    class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>
                        <div class="topbar-item">
                            <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome!</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="topbar-item">
                            <button type="button" class="topbar-button" id="light-dark-mode">
                                <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>
                        <form class="app-search d-none d-md-block ms-2">
                            <div class="position-relative">
                                <input type="search" class="form-control" placeholder="Search..." autocomplete="off"
                                    value="">
                                <iconify-icon icon="solar:magnifer-linear" class="search-widget-icon"></iconify-icon>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-nav">
            <div class="logo-box">


            </div>

            <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
                <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"
                    class="button-sm-hover-icon"></iconify-icon>
            </button>

            <div class="scrollbar" data-simplebar>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title">General</li>

                    <li class="nav-item">
                        <a class="nav-link xyxbtn123" href="#" data-id="box1">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarProducts">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">Home</span>
                        </a>
                        <div class="collapse show" id="sidebarProducts">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#" data-id="box3">All
                                        Product List</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#" data-id="box4">Main
                                        Slider</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#" data-id="box5">About
                                        Cards</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#"
                                        data-id="box6">Property Highlights</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#"
                                        data-id="box7">Features list</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#"
                                        data-id="box8">Videos</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#"
                                        data-id="box9">Advertisement</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#" data-id="box10">Plan
                                        Room</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#" data-id="box11">Why
                                        Choose Us</a></li>
                                <li class="sub-nav-item"><a class="sub-nav-link xyxbtn123" href="#"
                                        data-id="box12">Services Card</a></li>
                            </ul>
                        </div>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="./about_manager.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">About</span>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="./add_service.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">Add Service</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="./requests.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">Requests</span>
                        </a>
                    </li>


                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-arrow" href="./admin_blocks.php">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:card-send-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text">Payments</span>
                            </a>
                        </li>
                    <?php endif; ?>





                    <li class="nav-item">
                        <a class="nav-link" href="./logout.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:logout-2-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>





        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-fluid">

                <!-- Start here.... -->
                <div class="row">
                    <div class="col-xxl-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-primary text-truncate mb-3" role="alert">
                                    We regret to inform you that our server is currently experiencing technical
                                    difficulties.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="avatar-md bg-soft-primary rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24">
                                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0-8 0M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">Total Visitors</p>
                                                <h3 class="text-dark mt-1 mb-0"><?= $total_visits ?></h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-success">


                                                    2.3%</span>
                                                <span class="text-muted ms-1 fs-12">Last Week</span>
                                            </div>
                                        </div>
                                    </div> <!-- end card body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                            <div class="col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="avatar-md bg-soft-primary rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1024" height="1024"
                                                        viewBox="0 0 1024 1024">
                                                        <path fill="currentColor" fill-rule="evenodd"
                                                            d="M464 144c8.837 0 16 7.163 16 16v304c0 8.836-7.163 16-16 16H160c-8.837 0-16-7.164-16-16V160c0-8.837 7.163-16 16-16zm-52 68H212v200h200zm493.333 87.686c6.248 6.248 6.248 16.379 0 22.627l-181.02 181.02c-6.248 6.248-16.378 6.248-22.627 0l-181.019-181.02c-6.248-6.248-6.248-16.379 0-22.627l181.02-181.02c6.248-6.248 16.378-6.248 22.627 0zm-84.853 11.313L713 203.52L605.52 311L713 418.48zM464 544c8.837 0 16 7.164 16 16v304c0 8.837-7.163 16-16 16H160c-8.837 0-16-7.163-16-16V560c0-8.836 7.163-16 16-16zm-52 68H212v200h200zm452-68c8.837 0 16 7.164 16 16v304c0 8.837-7.163 16-16 16H560c-8.837 0-16-7.163-16-16V560c0-8.836 7.163-16 16-16zm-52 68H612v200h200z" />
                                                    </svg>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">User</p>
                                                <h3 class="text-dark mt-1 mb-0">
                                                    <div id="visitorCount"></div>
                                                </h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-success">
                                                    8.1%</span>
                                                <span class="text-muted ms-1 fs-12">Last Month</span>
                                            </div>
                                        </div>
                                    </div> <!-- end card body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                            <div class="col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="avatar-md bg-soft-primary rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24">
                                                        <g fill="none" stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="1.5">
                                                            <path
                                                                d="M6.053 20.25h3m-3-15.75v9M.803 9h10.5M3.803.75l2.25 3.75M8.303.75L6.053 4.5M15.8 23.25a3 3 0 0 0-3-3H9.053a3 3 0 0 0-3-3H.8v6zM1.303 4.5h9.5s.5 0 .5.5v8s0 .5-.5.5h-9.5s-.5 0-.5-.5V5s0-.5.5-.5M13.86 8.575l.357-2.675a.75.75 0 0 1 .743-.651h6.187a.75.75 0 0 1 .743.651l1.3 9.75a.764.764 0 0 1-.743.849H12.8" />
                                                            <path d="M15.8 5.25v-1.5a2.25 2.25 0 0 1 4.5 0v1.5" />
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">Product</p>
                                                <h3 class="text-dark mt-1 mb-0"><span id="countryCount">0</span>
                                                </h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-danger">
                                                    0.3%</span>
                                                <span class="text-muted ms-1 fs-12">Last Month</span>
                                            </div>
                                        </div>
                                    </div> <!-- end card body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                            <div class="col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="avatar-md bg-soft-primary rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24">
                                                        <path fill="currentColor" fill-rule="evenodd"
                                                            d="m12.6 11.503l3.891 3.891l-.848.849L11.4 12V6h1.2zM12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m0-1.2a8.8 8.8 0 1 0 0-17.6a8.8 8.8 0 0 0 0 17.6" />
                                                    </svg>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">time</p>
                                                <h3 class="text-dark mt-1 mb-0" id="current-time"></h3>

                                                <script>
                                                    function updateTime() {
                                                        const now = new Date();
                                                        let hours = now.getHours();
                                                        const minutes = now.getMinutes();
                                                        const ampm = hours >= 12 ? '' : '';

                                                        hours = hours % 12;
                                                        hours = hours ? hours : 12; // لو الساعة 0 تبقى 12
                                                        const minutesStr = minutes < 10 ? '0' + minutes : minutes;

                                                        const timeStr = hours + ':' + minutesStr + ' ' + ampm;
                                                        document.getElementById('current-time').textContent = timeStr;
                                                    }

                                                    updateTime();
                                                    setInterval(updateTime, 60000); // تحدث كل دقيقة
                                                </script>

                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-danger">
                                                    10.6%</span>
                                                <span class="text-muted ms-1 fs-12">Last Month</span>
                                            </div>
                                        </div>
                                    </div> <!-- end card body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                    </div> <!-- end col -->

                    <div class="col-xxl-7">
                        <div class="card">
                            <div class="card-body">
                                <div id="chart"></div>
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div> <!-- end col -->
                </div> <!-- end row -->

                <div class="row ptn_box_open" id="box1">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title">
                                        User Information
                                    </h4>


                                </div>
                            </div>
                            <!-- end card body -->
                            <div class="table-responsive table-centered">
                                <table class="table mb-0">
                                    <thead class="bg-light bg-opacity-50">
                                        <tr>
                                            <th>
                                                User
                                            </th>
                                            <th class="ps-3">
                                                ID
                                            </th>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                phone
                                            </th>
                                            <th>
                                                Data
                                            </th>
                                            <th>
                                                time
                                            </th>



                                        </tr>
                                    </thead>
                                    <!-- end thead-->
                                    <tbody>
                                        <?php
                                        $visitors = $conn->query("SELECT id, name, phone, created_at FROM visitors ORDER BY id DESC");
                                        ?>

                                        <?php if ($visitors && $visitors->num_rows > 0): ?>
                                            <?php while ($visitor = $visitors->fetch_assoc()): ?>


                                                <tr>
                                                    <td>.</td>
                                                    <td class="ps-3">
                                                        <a href="order-detail.html">#<?= (int) $visitor['id'] ?></a>
                                                    </td>
                                                    <td><?= htmlspecialchars($visitor['name']) ?></td>

                                                    <td><?= htmlspecialchars($visitor['phone']) ?></td>
                                                    <td><?= date('Y-m-d', strtotime($visitor['created_at'])) ?></td>
                                                    <td><?= date('H:i:s', strtotime($visitor['created_at'])) ?></td>


                                                </tr>

                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <ul class="text-center">
                                                <li>No user</li>
                                            </ul>
                                        <?php endif; ?>



                                    </tbody>
                                    <!-- end tbody -->
                                </table>
                                <!-- end table -->
                            </div>
                            <!-- table responsive -->

                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div> <!-- end row -->



                <div class="row ptn_box_open" id="box3">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                                <h4 class="card-title flex-grow-1">All Product List</h4>

                                <a href="./add_project.php" class="btn btn-sm btn-primary">
                                    Add Product
                                </a>


                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover table-centered">
                                        <thead class="bg-light-subtle">
                                            <tr>
                                                <th style="width: 20px;">
                                                    <div class="form-check ms-1">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="customCheck1">
                                                        <label class="form-check-label" for="customCheck1"></label>
                                                    </div>
                                                </th>
                                                <th>&amp; Product </th>
                                                <th>Price</th>

                                                <th>Category</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="countriesTableBody">



                                            <?php if ($result && $result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr id="row-<?= $row['id'] ?>">
                                                        <td>
                                                            <div class="form-check ms-1">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="check-<?= $row['id'] ?>">
                                                                <label class="form-check-label"
                                                                    for="check-<?= $row['id'] ?>">&nbsp;</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                                    <?php if (!empty($row['image'])): ?>
                                                                        style="background-image: url('/Egy-Hills/uploads/<?= urlencode(htmlspecialchars($row['image'])) ?>'); background-size: cover; background-position: center;"
                                                                    <?php else: ?>
                                                                        style="background-color: #e9ecef; width: 80px; height: 80px;"
                                                                    <?php endif; ?>>
                                                                    <?php if (empty($row['image'])): ?>
                                                                        <div class="bg-secondary rounded"
                                                                            style="width: 80px; height: 80px;"></div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>
                                                                    <a href="#!"
                                                                        class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']) ?></a>
                                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Location:
                                                                        </span><?= htmlspecialchars($row['location']) ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= htmlspecialchars($row['price']) ?></td>

                                                        <td>Real Estate</td>

                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button onclick="deleteProject(<?= $row['id'] ?>)"
                                                                    class="btn btn-soft-danger btn-sm">
                                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                        class="align-middle fs-18"></iconify-icon>
                                                                </button>





                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No projects added yet.</td>
                                                </tr>
                                            <?php endif; ?>


                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive -->
                            </div>



                        </div>
                    </div>



                </div>


                <div class="row ">

                    <!-- الكارد 1 -->
                    <div class="col-xl-12 ptn_box_open" id="box4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                                <h4 class="card-title flex-grow-1">Main Slider
                                </h4>
                                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addProductModal-1">Add Product</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover table-centered">
                                    <thead class="bg-light-subtle">
                                        <tr>
                                            <th style="width: 20px;">
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input" id="customCheck1-1">
                                                    <label class="form-check-label" for="customCheck1-1"></label>
                                                </div>
                                            </th>
                                            <th>Product</th>

                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php while ($row = $sliders->fetch_assoc()): ?>
                                            <tr id="row-1">

                                                <td>
                                                    <div class="form-check ms-1">
                                                        <input type="checkbox" class="form-check-input" id="check-1">
                                                        <label class="form-check-label" for="check-1">&nbsp;</label>
                                                    </div>
                                                </td>



                                                <td>
                                                    <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                                    </div>
                                                </td>



                                                <td>
                                                    <div class="d-flex gap-2">

                                                        <a href="?delete_slider=<?= intval($row['id']) ?>">

                                                            <button class="btn btn-soft-danger btn-sm">
                                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                    class="align-middle fs-18"></iconify-icon>
                                                            </button>
                                                        </a>

                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>

                                </div>
                            <?php endwhile; ?>

                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- الكارد 2 -->
                <div class="col-xl-12 ptn_box_open" id="box5">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center gap-1">
                            <h4 class="card-title flex-grow-1">About Cards</h4>
                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProductModal-2">Add Product</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover table-centered">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck1-2">
                                                <label class="form-check-label" for="customCheck1-2"></label>
                                            </div>
                                        </th>
                                        <th>Product Name &amp; Size</th>
                                        <th>title</th>
                                        <th>description</th>
                                        <th>link</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php while ($row = $about_cards->fetch_assoc()): ?>

                                        <tr id="row-2">
                                            <td>
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input" id="check-2">
                                                    <label class="form-check-label" for="check-2">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                                </div>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['title']) ?>
                                            </td>



                                            <td>
                                                <?= htmlspecialchars($row['description']) ?>
                                            </td>

                                            <td>
                                                <a href=" <?= htmlspecialchars($row['link']) ?>">link</a>
                                            </td>


                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?delete_about_card=<?= intval($row['id']) ?>">

                                                        <button class="btn btn-soft-danger btn-sm">
                                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                class="align-middle fs-18"></iconify-icon>
                                                        </button>
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>

                            </div>
                        <?php endwhile; ?>


                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الكارد 3 -->
            <div class="col-xl-12 ptn_box_open" id="box6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">Property Highlights</h4>
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addProductModal-3">Add Product</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck1-3">
                                            <label class="form-check-label" for="customCheck1-3"></label>
                                        </div>
                                    </th>
                                    <th>Product Name &amp; Size</th>
                                    <th>titel</th>
                                    <th>description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $highlights->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input"
                                                    id="check-<?= intval($row['id']) ?>">
                                                <label class="form-check-label"
                                                    for="check-<?= intval($row['id']) ?>">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover; background-position: center;">

                                            </div>
                                        </td>

                                        <td> <?= htmlspecialchars($row['title']) ?></td>

                                        <td> <?= htmlspecialchars($row['description']) ?></td>




                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?delete_highlight=<?= intval($row['id']) ?>">

                                                    <button class="btn btn-soft-danger btn-sm">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </button>
                                            </div></a>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- الرابع -->
            <!-- العنصر 4 -->
            <div class="card mb-4 ptn_box_open" id="box7">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Features list</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-4">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck4">
                                            <label class="form-check-label" for="customCheck4"></label>
                                        </div>
                                    </th>
                                    <th>Product </th>
                                    <th>name</th>

                                    <th>Category</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>



                            <tbody>
                                <?php if ($property_highlights && $property_highlights->num_rows > 0): ?>
                                    <?php $i = 1;
                                    while ($row = $property_highlights->fetch_assoc()): ?>
                                        <tr id="row-<?= $i ?>">
                                            <td>
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input" id="check-<?= $i ?>">
                                                    <label class="form-check-label" for="check-<?= $i ?>">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover;">
                                                </div>
                                            </td>

                                            <td><?= htmlspecialchars($row['title']) ?></td>

                                            <td>Real Estate</td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?delete_property_highlight=<?= intval($row['id']) ?>"
                                                        class="btn btn-soft-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; endwhile; ?>
                                <?php else: ?>
                                    <!-- <tr>
                                        <td colspan="7">
                                            <div class="alert alert-warning text-center mb-0">🚫 No Property Highlights
                                                available yet.</div>
                                        </td>
                                    </tr> -->
                                <?php endif; ?>
                            </tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 5 -->
            <div class="card mb-4 ptn_box_open" id="box8">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Videos</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-5">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck5">
                                            <label class="form-check-label" for="customCheck5"></label>
                                        </div>
                                    </th>
                                    <th>link</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($videos && $videos->num_rows > 0): ?>
                                    <?php $i = 1;
                                    while ($row = $videos->fetch_assoc()): ?>
                                        <tr id="row-<?= $i ?>">
                                            <td>
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input" id="check-<?= $i ?>">
                                                    <label class="form-check-label" for="check-<?= $i ?>">&nbsp;</label>
                                                </div>
                                            </td>

                                            <td colspan="5">
                                                <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank"
                                                    class="text-primary">
                                                    <?= htmlspecialchars($row['url']) ?>
                                                </a>
                                            </td>


                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?delete_video=<?= intval($row['id']) ?>"
                                                        class="btn btn-soft-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this video?');">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; endwhile; ?>
                                <?php else: ?>
                                    <!-- <tr>
                                        <td colspan="7">
                                            <div class="alert alert-warning text-center mb-0">🚫 No videos available yet.
                                            </div>
                                        </td>
                                    </tr> -->
                                <?php endif; ?>


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 6 -->
            <!-- موجود بالفعل قبل كده عندك -->

            <!-- العنصر 7 -->
            <div class="card mb-4 ptn_box_open" id="box9">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Advertisement</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-7">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">

                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck7">
                                            <label class="form-check-label" for="customCheck7"></label>
                                        </div>
                                    </th>
                                    <th>Product </th>
                                    <th>Name</th>

                                    <th>Category</th>

                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                <?php if ($ads && $ads->num_rows > 0): ?>
                                    <?php $i = 1;
                                    while ($row = $ads->fetch_assoc()): ?>
                                        <tr id="row-<?= $i ?>">
                                            <td>
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input" id="check-<?= $i ?>">
                                                    <label class="form-check-label" for="check-<?= $i ?>">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover;">
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>

                                            <td>Real Estate</td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?delete_ad=<?= intval($row['id']) ?>"
                                                        class="btn btn-soft-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this ad?');">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; endwhile; ?>
                                <?php endif; ?>

                                <?php if ($ad_icons && $ad_icons->num_rows > 0): ?>
                                    <?php while ($icon = $ad_icons->fetch_assoc()): ?>
                                        <tr id="row-icon-<?= intval($icon['id']) ?>">
                                            <td>
                                                <div class="form-check ms-1">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="check-icon-<?= intval($icon['id']) ?>">
                                                    <label class="form-check-label"
                                                        for="check-icon-<?= intval($icon['id']) ?>">&nbsp;</label>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center size_"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($icon['icon']) ?>'); background-size: cover;">

                                                </div>

                                            </td>




                                            <td>
                                                <?= htmlspecialchars($icon['text']) ?>
                                            </td>


                                            <td>
                                                <?= htmlspecialchars($icon['title']) ?>
                                            </td>


                                            <td>

                                                <a href="?delete_ad_icon=<?= intval($icon['id']) ?>"
                                                    class="btn btn-soft-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this icon?');">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </a>
                                            </td>



                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 8 -->
            <div class="card mb-4 ptn_box_open" id="box10">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Plan Room</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-8">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck8">
                                            <label class="form-check-label" for="customCheck8"></label>
                                        </div>
                                    </th>
                                    <th>Product </th>
                                    <th>Name</th>
                                    <th>description</th>


                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php while ($row = $plan_and_room->fetch_assoc()): ?>
                                    <tr id="row-8">


                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="check-8">
                                                <label class="form-check-label" for="check-8">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>)">
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>

                                        <td> <?= htmlspecialchars($row['description']) ?></td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?delete_plan_and_room=<?= intval($row['id']) ?>"> <button
                                                        class="btn btn-soft-danger btn-sm">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </button></a>


                                            </div>
                                        </td>
                                    </tr>
                                    <tr>



                                    </tr>
                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 9 -->
            <div class="card mb-4 ptn_box_open" id="box11">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Why Choose Us</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-9">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck9">
                                            <label class="form-check-label" for="customCheck9"></label>
                                        </div>
                                    </th>
                                    <th>question</th>



                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $questions->fetch_assoc()): ?>
                                    <tr id="row-<?= intval($row['id']) ?>">
                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input"
                                                    id="check-<?= intval($row['id']) ?>">
                                                <label class="form-check-label"
                                                    for="check-<?= intval($row['id']) ?>">&nbsp;</label>
                                            </div>
                                        </td>


                                        <td><?= htmlspecialchars($row['question'] ?? 'Real Estate') ?></td>


                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?delete_question=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm"
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

            <!-- العنصر 10 -->
            <div class="card mb-4 ptn_box_open" id="box12">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Services Card
                    </h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-10">
                        Add Product
                    </a>
                </div>
                <div>
                    <div class="table-responsive">

                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="customCheck10">
                                            <label class="form-check-label" for="customCheck10"></label>
                                        </div>
                                    </th>
                                    <th>Product </th>
                                    <th>Name</th>
                                    <th>description</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $services->fetch_assoc()): ?>
                                    <tr id="row-10">


                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="check-10">
                                                <label class="form-check-label" for="check-10">&nbsp;</label>
                                            </div>
                                        </td>


                                        <td>
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['icon']) ?>)">
                                            </div>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($row['title']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['description']) ?>
                                        </td>


                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?delete_service=<?= intval($row['id']) ?>">
                                                    <button class="btn btn-soft-danger btn-sm">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </button>

                                                </a>

                                            </div>
                                        </td>


                                    <?php endwhile; ?>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>




        </div>


        <div class="modal fade" id="addProductModal-1" tabindex="-1" aria-labelledby="modalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Main Slider
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <input type="file" name="image" class="form-control" required>
                                <button name="add_slider" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="addProductModal-2" tabindex="-1" aria-labelledby="modalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">About Cards</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


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
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal-3" tabindex="-1" aria-labelledby="modalLabel3" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Property Highlights</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal-4" tabindex="-1" aria-labelledby="addProductModalLabel-4"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Features list</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="mb-5">
                            <div class="mb-3">
                                <input type="file" name="image" id="highlightImage" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="title" id="highlightTitle" class="form-control"
                                    placeholder="Enter Title" required>
                            </div>
                            <button type="submit" name="add_property_highlight" class="btn btn-primary">Add
                                Features list
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addProductModal-5" tabindex="-1" aria-labelledby="addProductModalLabel-5"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Videos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" class="mb-3">
                            <div class="mb-3 d-flex align-items-center gap-3">
                                <input type="url" name="url" placeholder="Video URL" class="form-control" required>
                                <button name="add_video" class="btn btn-primary">Add</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="addProductModal-7" tabindex="-1" aria-labelledby="addProductModalLabel-7"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Advertisement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

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


                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal-8" tabindex="-1" aria-labelledby="addProductModalLabel-8"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Plan Room
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                            <button type="submit" name="add_plan_and_room" class="btn btn-primary">Add Plan
                                and
                                Room</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal-9" tabindex="-1" aria-labelledby="addProductModalLabel-9"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Why Choose Us</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <input type="text" name="question" placeholder="Question" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="answer" placeholder="Answer" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3" style="display: none;">
                                <input type="file" name="image" class="form-control">
                            </div>
                            <button name="add_question" class="btn btn-primary">Add Q&A</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal-10" tabindex="-1" aria-labelledby="addProductModalLabel-10"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Services Card</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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

                    </div>
                </div>
            </div>
        </div>



    </div>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">

                    &copy; Larkon. Crafted by Ammar
                </div>
            </div>
        </div>
    </footer>

    </div>


    </div>
    <script>

        function deleteProject(id) {
            if (!confirm("Are you sure you want to delete this project?")) return;

            fetch('delete_project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        const card = document.getElementById('card-' + id);
                        if (card) card.remove();
                    } else {
                        alert('❌ An error occurred while deleting.');
                    }
                });
        }

        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            if (form) form.classList.toggle('d-none');
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
                        alert('✅ Changes saved successfully.');
                        location.reload();
                    } else {
                        alert('❌ An error occurred while updating.');
                    }
                });
        }
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const savedBoxId = localStorage.getItem('selectedBoxId');
            if (savedBoxId) {
                document.querySelectorAll('.ptn_box_open').forEach(div => {
                    div.style.display = (div.id === savedBoxId) ? 'block' : 'none';
                });
            }

            document.querySelectorAll('.xyxbtn123').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    localStorage.setItem('selectedBoxId', id);
                    document.querySelectorAll('.ptn_box_open').forEach(div => {
                        div.style.display = (div.id === id && div.style.display !== 'block') ? 'block' : 'none';
                    });
                });
            });

            const body = document.querySelector("body");
            const sidebar = body.querySelector("nav");
            const toggleBtn = body.querySelector(".sidebar-toggle");

            if (sidebar && toggleBtn) {
                const sidebarStatus = localStorage.getItem("status");
                if (sidebarStatus === "close") {
                    sidebar.classList.add("close");
                }

                toggleBtn.addEventListener("click", () => {
                    sidebar.classList.toggle("close");
                    localStorage.setItem("status", sidebar.classList.contains("close") ? "close" : "open");
                });
            }
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tableBody = document.querySelector("tbody");
            const rowCount = tableBody.querySelectorAll("tr").length;
            console.log("Total Visitors:", rowCount);
            document.getElementById("visitorCount").textContent = + rowCount;
        });
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const tbody = document.getElementById("countriesTableBody");
            const rowCount = tbody.querySelectorAll("tr[id^='row-']").length;
            const countElement = document.getElementById("countryCount");
            if (countElement) {
                countElement.textContent = rowCount;
            }
            console.log("عدد الدول:", rowCount);
        });
    </script>
    <script src="assets/js/vendor.js"></script>
    <script src="assets/js/dashboard.js"></script>

</body>

</html>