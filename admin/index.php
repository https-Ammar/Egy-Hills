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
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Dashboard | Larkon - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Vendor css (Require in all Page) -->
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="assets/js/config.js"></script>

</head>

<body>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        <header class="topbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <div class="d-flex align-items-center">
                        <!-- Menu Toggle Button -->
                        <div class="topbar-item">
                            <button type="button" class="button-toggle-menu me-2">
                                <iconify-icon icon="solar:hamburger-menu-broken"
                                    class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>

                        <!-- Menu Toggle Button -->
                        <div class="topbar-item">
                            <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome!</h4>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1">

                        <!-- Theme Color (Light/Dark) -->
                        <div class="topbar-item">
                            <button type="button" class="topbar-button" id="light-dark-mode">
                                <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>




                        <!-- App Search-->
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


        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        <div class="main-nav">
            <!-- Sidebar Logo -->
            <div class="logo-box">
                <a href="index.html" class="logo-dark">
                    <img src="assets/images/logo-sm.png" class="logo-sm" alt="logo sm">
                    <img src="assets/images/logo-dark.png" class="logo-lg" alt="logo dark">
                </a>

                <a href="index.html" class="logo-light">
                    <img src="assets/images/logo-sm.png" class="logo-sm" alt="logo sm">
                    <img src="assets/images/logo-light.png" class="logo-lg" alt="logo light">
                </a>
            </div>

            <!-- Menu Toggle Button (sm-hover) -->
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
                            <span class="nav-text"> Dashboard </span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarProducts">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Products </span>
                        </a>
                        <div class="collapse" id="sidebarProducts">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="product-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="product-grid.html">Grid</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="product-details.html">Details</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="product-edit.html">Edit</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="product-add.html">Create</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarCategory" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarCategory">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Category </span>
                        </a>
                        <div class="collapse" id="sidebarCategory">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="category-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="category-edit.html">Edit</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="category-add.html">Create</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="./requests.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> requests </span>
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




                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarOrders" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarOrders">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Orders </span>
                        </a>
                        <div class="collapse" id="sidebarOrders">
                            <ul class="nav sub-navbar-nav">

                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="orders-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="order-detail.html">Details</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="order-cart.html">Cart</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="order-checkout.html">Check Out</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarPurchases" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarPurchases">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:card-send-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Purchases </span>
                        </a>
                        <div class="collapse" id="sidebarPurchases">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="purchase-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="purchase-order.html">Order</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="purchase-returns.html">Return</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarAttributes" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarAttributes">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:confetti-minimalistic-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Attributes </span>
                        </a>
                        <div class="collapse" id="sidebarAttributes">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="attributes-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="attributes-edit.html">Edit</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="attributes-add.html">Create</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#sidebarInvoice" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarInvoice">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Invoices </span>
                        </a>
                        <div class="collapse" id="sidebarInvoice">
                            <ul class="nav sub-navbar-nav">
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="invoice-list.html">List</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="invoice-details.html">Details</a>
                                </li>
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="invoice-add.html">Create</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="./logout.php">
                            <span class="nav-icon">
                                <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Settings </span>
                        </a>
                    </li>


                </ul>
            </div>
        </div>
        <!-- ========== App Menu End ========== -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
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
                                                    <iconify-icon icon="solar:cart-5-bold-duotone"
                                                        class="avatar-title fs-32 text-primary"></iconify-icon>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">Total Orders</p>
                                                <h3 class="text-dark mt-1 mb-0"><?= $total_visits ?></h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-success"> <i class="bx bxs-up-arrow fs-12"></i>
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
                                                    <i class="bx bx-award avatar-title fs-24 text-primary"></i>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">New Leads</p>
                                                <h3 class="text-dark mt-1 mb-0">9,56</h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-success"> <i class="bx bxs-up-arrow fs-12"></i>
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
                                                    <i class="bx bxs-backpack avatar-title fs-24 text-primary"></i>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">Deals</p>
                                                <h3 class="text-dark mt-1 mb-0">976</h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-danger"> <i class="bx bxs-down-arrow fs-12"></i>
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
                                                    <i class="bx bx-dollar-circle avatar-title text-primary fs-24"></i>
                                                </div>
                                            </div> <!-- end col -->
                                            <div class="col-6 text-end">
                                                <p class="text-muted mb-0 text-truncate">Booked Revenue</p>
                                                <h3 class="text-dark mt-1 mb-0">$16k</h3>
                                            </div> <!-- end col -->
                                        </div> <!-- end row-->
                                    </div> <!-- end card body -->
                                    <div class="card-footer py-2 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="text-danger"> <i class="bx bxs-down-arrow fs-12"></i>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Performance</h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-light">ALL</button>
                                        <button type="button" class="btn btn-sm btn-outline-light">1M</button>
                                        <button type="button" class="btn btn-sm btn-outline-light">6M</button>
                                        <button type="button" class="btn btn-sm btn-outline-light active">1Y</button>
                                    </div>
                                </div> <!-- end card-title-->

                                <div dir="ltr">
                                    <div id="dash-performance-chart" class="apex-charts"></div>
                                </div>
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
                                        Recent Orders
                                    </h4>

                                    <a href="#!" class="btn btn-sm btn-soft-primary">
                                        <i class="bx bx-plus me-1"></i>Create Order
                                    </a>
                                </div>
                            </div>
                            <!-- end card body -->
                            <div class="table-responsive table-centered">
                                <table class="table mb-0">
                                    <thead class="bg-light bg-opacity-50">
                                        <tr>
                                            <th class="ps-3">
                                                Order ID.
                                            </th>
                                            <th>
                                                Date
                                            </th>
                                            <th>
                                                Product
                                            </th>
                                            <th>
                                                Customer Name
                                            </th>
                                            <th>
                                                Email ID
                                            </th>
                                            <th>
                                                Phone No.
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
                                                    <td class="ps-3">
                                                        <a href="order-detail.html">#<?= (int) $visitor['id'] ?></a>
                                                    </td>
                                                    <td><?= htmlspecialchars($visitor['name']) ?></td>

                                                    <td><?= htmlspecialchars($visitor['phone']) ?></td>
                                                    <td><?= date('Y-m-d', strtotime($visitor['created_at'])) ?></td>
                                                    <td><?= date('H:i:s', strtotime($visitor['created_at'])) ?></td>
                                                    <td>Credit Card</td>

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


                <!-- king mero -->
                <!-- king mero -->
                <!-- king mero -->
                <!-- king mero -->
                <!-- king mero -->


                <div class="row">
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
                                                <th>Product Name &amp; Size</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Category</th>
                                                <th>Rating</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>



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
                                                                    style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>)">
                                                                    <?php if (!empty($row['image'])): ?>

                                                                    <?php else: ?>
                                                                        <div class="bg-secondary rounded"
                                                                            style="width: 80px; height: 80px; "></div>
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
                                                        <td>$<?= htmlspecialchars($row['price']) ?></td>
                                                        <td>
                                                            <p class="mb-1 text-muted"><span
                                                                    class="text-dark fw-medium">Available</span></p>
                                                            <p class="mb-0 text-muted">-</p>
                                                        </td>
                                                        <td>Real Estate</td>
                                                        <td>
                                                            <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                                <i
                                                                    class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                                4.5
                                                            </span> 0 Reviews
                                                        </td>
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


                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->



                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->

                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->
                    <!-- king mero -->


                </div>

                <!-- king mero -->
                <!-- king mero -->
                <div class="row">

                    <!-- الكارد 1 -->
                    <div class="col-xl-12">
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
                                            <th>Product Name &amp; Size</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Category</th>
                                            <th>Rating</th>
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
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                            style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                                        </div>
                                                        <div>
                                                            <a href="#!" class="text-dark fw-medium fs-15">Project Title</a>
                                                            <p class="text-muted mb-0 mt-1 fs-13"><span>Location:</span>
                                                                Project Title</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$1000</td>
                                                <td>
                                                    <p class="mb-1 text-muted"><span
                                                            class="text-dark fw-medium">Available</span></p>
                                                    <p class="mb-0 text-muted">-</p>
                                                </td>
                                                <td>Real Estate</td>
                                                <td>
                                                    <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                        <i
                                                            class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>4.5
                                                    </span> 0 Reviews
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
                <div class="col-xl-12">
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
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Category</th>
                                        <th>Rating</th>
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
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>');">
                                                    </div>
                                                    <div>
                                                        <a href="#!" class="text-dark fw-medium fs-15">Project Title</a>
                                                        <p class="text-muted mb-0 mt-1 fs-13"><span>Location:</span>
                                                            Project Title</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$1000</td>
                                            <td>
                                                <p class="mb-1 text-muted"><span
                                                        class="text-dark fw-medium">Available</span></p>
                                                <p class="mb-0 text-muted">-</p>
                                            </td>
                                            <td>Real Estate</td>
                                            <td>
                                                <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                    <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>4.5
                                                </span> 0 Reviews
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
            <div class="col-xl-12">
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
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover; background-position: center;">

                                                </div>
                                                <div>
                                                    <a href="#!"
                                                        class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']) ?></a>
                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Location:</span>
                                                        <?= htmlspecialchars($row['title']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$1000</td>
                                        <td>
                                            <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                            </p>
                                            <p class="mb-0 text-muted">-</p>
                                        </td>
                                        <td>Real Estate</td>
                                        <td>
                                            <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>4.5
                                            </span> 0 Reviews
                                        </td>
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
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover;">
                                                    </div>
                                                    <div>
                                                        <a href="#!"
                                                            class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']) ?></a>
                                                        <p class="text-muted mb-0 mt-1 fs-13"><span>Location:
                                                            </span><?= htmlspecialchars($row['title']) ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$1000</td>
                                            <td>
                                                <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                                </p>
                                                <p class="mb-0 text-muted">-</p>
                                            </td>
                                            <td>Real Estate</td>
                                            <td>
                                                <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                    <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                    4.5
                                                </span> 0 Reviews
                                            </td>
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
                                    <tr>
                                        <td colspan="7">
                                            <div class="alert alert-warning text-center mb-0">🚫 No Property Highlights
                                                available yet.</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 5 -->
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                    <tr>
                                        <td colspan="7">
                                            <div class="alert alert-warning text-center mb-0">🚫 No videos available yet.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 6 -->
            <!-- موجود بالفعل قبل كده عندك -->

            <!-- العنصر 7 -->
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                        style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover;">
                                                    </div>
                                                    <div>
                                                        <a href="#!"
                                                            class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']) ?></a>
                                                        <p class="text-muted mb-0 mt-1 fs-13"><span>Location:
                                                            </span><?= htmlspecialchars($row['title']) ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$1000</td>
                                            <td>
                                                <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                                </p>
                                                <p class="mb-0 text-muted">-</p>
                                            </td>
                                            <td>Real Estate</td>
                                            <td>
                                                <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                    <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                    4.5
                                                </span> 0 Reviews
                                            </td>
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
                                                <div class="d-flex align-items-center gap-2">
                                                    <div
                                                        class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                        <img src="/Egy-Hills/uploads/<?= htmlspecialchars($icon['icon']) ?>"
                                                            width="50" height="50" class="img-thumbnail rounded" />
                                                    </div>
                                                    <div>
                                                        <a href="#!"
                                                            class="text-dark fw-medium fs-15"><?= htmlspecialchars($icon['title']) ?></a>
                                                        <p class="text-muted mb-0 mt-1 fs-13">
                                                            <?= htmlspecialchars($icon['text']) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$1000</td>
                                            <td>
                                                <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                                </p>
                                                <p class="mb-0 text-muted">-</p>
                                            </td>
                                            <td>Real Estate</td>
                                            <td>
                                                <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                    <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                    4.5
                                                </span> 0 Reviews
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?delete_ad_icon=<?= intval($icon['id']) ?>"
                                                        class="btn btn-soft-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this icon?');">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </a>
                                                </div>
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
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>)">
                                                </div>
                                                <div>
                                                    <a href="#!"
                                                        class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['title']) ?></a>
                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Location: </span>
                                                        <?= htmlspecialchars($row['description']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$1000</td>
                                        <td>
                                            <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                            </p>
                                            <p class="mb-0 text-muted">-</p>
                                        </td>
                                        <td>Real Estate</td>
                                        <td>
                                            <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                4.5
                                            </span> 0 Reviews
                                        </td>
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
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
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
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                    style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>'); background-size: cover;">
                                                </div>
                                                <div>
                                                    <a href="#!"
                                                        class="text-dark fw-medium fs-15"><?= htmlspecialchars($row['question']) ?></a>
                                                    <p class="text-muted mb-0 mt-1 fs-13">
                                                        <span>Location:
                                                        </span><?= htmlspecialchars($row['location'] ?? 'N/A') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?= htmlspecialchars($row['price'] ?? '0') ?></td>
                                        <td>
                                            <p class="mb-1 text-muted">
                                                <span class="text-dark fw-medium">Available</span>
                                            </p>
                                            <p class="mb-0 text-muted">-</p>
                                        </td>
                                        <td><?= htmlspecialchars($row['category'] ?? 'Real Estate') ?></td>
                                        <td>
                                            <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                                <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                <?= htmlspecialchars($row['rating'] ?? '4.5') ?>
                                            </span> <?= htmlspecialchars($row['reviews'] ?? '0') ?> Reviews
                                        </td>
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
            <div class="card mb-4">
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
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="row-10">
                                    <?php while ($row = $services->fetch_assoc()): ?>
                                        <div class="row align-items-center border rounded p-3 mb-3">
                                            <div class="col-md-2 text-center mb-2 mb-md-0">
                                                <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['icon']) ?>"
                                                    width="60" class="img-thumbnail">
                                            </div>
                                            <div class="col-md-3">
                                                <strong><?= htmlspecialchars($row['title']) ?></strong>
                                            </div>
                                            <div class="col-md-5">
                                                <p class="mb-0"><?= htmlspecialchars($row['description']) ?></p>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="?delete_service=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this item?')">🗑️
                                                    Delete</a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-10">
                                            <label class="form-check-label" for="check-10">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url(/Egy-Hills/uploads/1750726719_pexels-heyho-6908502.jpg)">
                                            </div>
                                            <div>
                                                <a href="#!" class="text-dark fw-medium fs-15">Project Title</a>
                                                <p class="text-muted mb-0 mt-1 fs-13"><span>Location: </span>Project
                                                    Title</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$1000</td>
                                    <td>
                                        <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                        </p>
                                        <p class="mb-0 text-muted">-</p>
                                    </td>
                                    <td>Real Estate</td>
                                    <td>
                                        <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                            <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                            4.5
                                        </span> 0 Reviews
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button onclick="deleteProject(10)" class="btn btn-soft-danger btn-sm">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                    class="align-middle fs-18"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- العنصر 11 -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Main Slider</h4>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addProductModal-11">
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
                                            <input type="checkbox" class="form-check-input" id="customCheck11">
                                            <label class="form-check-label" for="customCheck11"></label>
                                        </div>
                                    </th>
                                    <th>Product Name &amp; Size</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="row-11">
                                    <td>
                                        <div class="form-check ms-1">
                                            <input type="checkbox" class="form-check-input" id="check-11">
                                            <label class="form-check-label" for="check-11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center"
                                                style="background-image: url(/Egy-Hills/uploads/1750726719_pexels-heyho-6908502.jpg)">
                                            </div>
                                            <div>
                                                <a href="#!" class="text-dark fw-medium fs-15">Project Title</a>
                                                <p class="text-muted mb-0 mt-1 fs-13"><span>Location: </span>Project
                                                    Title</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$1000</td>
                                    <td>
                                        <p class="mb-1 text-muted"><span class="text-dark fw-medium">Available</span>
                                        </p>
                                        <p class="mb-0 text-muted">-</p>
                                    </td>
                                    <td>Real Estate</td>
                                    <td>
                                        <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                            <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                            4.5
                                        </span> 0 Reviews
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button onclick="deleteProject(11)" class="btn btn-soft-danger btn-sm">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                    class="align-middle fs-18"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div>

        <!-- مودالات فاضية -->
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



        <!-- Modal 4 -->
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
                                Property
                                Highlight</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 5 -->
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


        <!-- Modal 7 -->
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

        <!-- Modal 8 -->
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

        <!-- Modal 9 -->
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
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control">
                            </div>
                            <button name="add_question" class="btn btn-primary">Add Q&A</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 10 -->
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

        <!-- Modal 11 -->
        <div class="modal fade" id="addProductModal-11" tabindex="-1" aria-labelledby="addProductModalLabel-11"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product - Slider 11</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- محتوى المودال هنا -->
                    </div>
                </div>
            </div>
        </div>



    </div>
    <!-- End Container Fluid -->

    <!-- ========== Footer Start ========== -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">

                    &copy; Larkon. Crafted by Ammar
                </div>
            </div>
        </div>
    </footer>
    <!-- ========== Footer End ========== -->

    </div>
    <!-- ==================================================== -->
    <!-- End Page Content -->
    <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="assets/js/app.js"></script>

    <!-- Vector Map Js -->
    <script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
    <script src="assets/vendor/jsvectormap/maps/world.js"></script>

    <!-- Dashboard Js -->
    <script src="assets/js/pages/dashboard.js"></script>

</body>

</html>








<button class="xyxbtn123" data-id="box1">Open Box 1</button>
<button class="xyxbtn123" data-id="box2">Open Box 2</button>

<div class="ptn_box_open" id="box1">This is Box 1</div>
<div class="ptn_box_open" id="box2">This is Box 2</div>

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
            sidebar = body.querySelector("nav"),
            sidebarToggle = body.querySelector(".sidebar-toggle");

        let getStatus = localStorage.getItem("status");
        if (getStatus === "close") {
            sidebar.classList.add("close");
        }

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