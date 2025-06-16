<?php
session_start();
include 'db.php';

// تحقق من الجلسة
if (empty($_SESSION['user_id'])) {
    header("Location:login.php");
    exit();
}

// === Upload Function ===
function uploadFile($file)
{
    if (!empty($file['name'])) {
        $name = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' . $name);
        return $name;
    }
    return '';
}

// === Helper for GET with default ===
function safeGet($key, $default = '')
{
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// === جميع عمليات الإضافة والحذف ===

// === Main Slider ===
if (!empty($_POST['add_slider'])) {
    $image = uploadFile($_FILES['image'] ?? []);
    $conn->query("INSERT INTO sliders (image) VALUES ('$image')");
}

if (!empty(safeGet('delete_slider'))) {
    $conn->query("DELETE FROM sliders WHERE id=" . intval(safeGet('delete_slider')));
}

// === About Slider ===
if (!empty($_POST['add_about_slider'])) {
    $image = uploadFile($_FILES['image'] ?? []);
    $conn->query("INSERT INTO about_slider (image) VALUES ('$image')");
}

if (!empty(safeGet('delete_about_slider'))) {
    $conn->query("DELETE FROM about_slider WHERE id=" . intval(safeGet('delete_about_slider')));
}

// === About Cards ===
if (!empty($_POST['add_about_card'])) {
    $image = uploadFile($_FILES['image'] ?? []);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');
    $link = $conn->real_escape_string($_POST['link'] ?? '');
    $conn->query("INSERT INTO about_cards (image, title, description, link) VALUES ('$image', '$title', '$desc', '$link')");
}

if (!empty(safeGet('delete_about_card'))) {
    $conn->query("DELETE FROM about_cards WHERE id=" . intval(safeGet('delete_about_card')));
}

// === Highlights ===
if (!empty($_POST['add_highlight'])) {
    $image = uploadFile($_FILES['image'] ?? []);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');
    $conn->query("INSERT INTO highlights (image, title, description) VALUES ('$image', '$title', '$desc')");
}

if (!empty(safeGet('delete_highlight'))) {
    $conn->query("DELETE FROM highlights WHERE id=" . intval(safeGet('delete_highlight')));
}

// === Videos ===
if (!empty($_POST['add_video'])) {
    $url = $conn->real_escape_string($_POST['url'] ?? '');
    $conn->query("INSERT INTO videos (url) VALUES ('$url')");
}

if (!empty(safeGet('delete_video'))) {
    $conn->query("DELETE FROM videos WHERE id=" . intval(safeGet('delete_video')));
}

// === Ads ===
if (!empty($_POST['add_ad'])) {
    $image = uploadFile($_FILES['image'] ?? []);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');
    $conn->query("INSERT INTO ads (image, title, description) VALUES ('$image', '$title', '$desc')");
}

if (!empty(safeGet('delete_ad'))) {
    $conn->query("DELETE FROM ads WHERE id=" . intval(safeGet('delete_ad')));
}

// === Ad Icons ===
if (!empty($_POST['add_ad_icon'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    $icon = uploadFile($_FILES['icon'] ?? []);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $text = $conn->real_escape_string($_POST['text'] ?? '');
    $conn->query("INSERT INTO ad_icons (ad_id, icon, title, text) VALUES ($ad_id, '$icon', '$title', '$text')");
}

if (!empty(safeGet('delete_ad_icon'))) {
    $conn->query("DELETE FROM ad_icons WHERE id=" . intval(safeGet('delete_ad_icon')));
}

// === Questions ===
if (!empty($_POST['add_question'])) {
    $question = $conn->real_escape_string($_POST['question'] ?? '');
    $answer = $conn->real_escape_string($_POST['answer'] ?? '');
    $image = uploadFile($_FILES['image'] ?? []);
    $conn->query("INSERT INTO questions (question, answer, image) VALUES ('$question', '$answer', '$image')");
}

if (!empty(safeGet('delete_question'))) {
    $conn->query("DELETE FROM questions WHERE id=" . intval(safeGet('delete_question')));
}

// === Services ===
if (!empty($_POST['add_service'])) {
    $icon = uploadFile($_FILES['icon'] ?? []);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');
    $conn->query("INSERT INTO services (icon, title, description) VALUES ('$icon', '$title', '$desc')");
}

if (!empty(safeGet('delete_service'))) {
    $conn->query("DELETE FROM services WHERE id=" . intval(safeGet('delete_service')));
}

// === Fetch Data ===
$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$ad_icons = $conn->query("SELECT * FROM ad_icons");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");

$section = htmlspecialchars(safeGet('section', ''));
?>
<!-- هنا تضع واجهة لوحة التحكم الخاصة بك -->











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
                <span class="logo_name">CodingLab</span>
                <a href="logout.php">Logout</a>
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


                            </div>


                            <!--  -->

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


                                    <!-- ✅ Ads بدون حقل Section -->
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
                                                    <!-- ✅ تم حذف عرض section -->
                                                    <a href="?delete_ad=<?= intval($row['id']) ?>"
                                                        class="btn btn-danger btn-sm mt-1">Delete</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <hr>

                                    <!-- ✅ Add Icon to Ad (بدون تغيير) -->
                                    <h3>Add Icon to Ad</h3>
                                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                                        <div class="mb-3">
                                            <label for="ad_id">Select Ad</label>
                                            <select name="ad_id" id="ad_id" class="form-select" required>
                                                <?php
                                                $ads2 = $conn->query("SELECT * FROM ads");
                                                if ($ads2 && $ads2->num_rows > 0) {
                                                    while ($a = $ads2->fetch_assoc()) {
                                                        $ad_id = intval($a['id']);
                                                        $ad_title = htmlspecialchars($a['title'] ?? 'No Title');
                                                        echo "<option value='$ad_id'>$ad_title</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No ads available</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="icon">Icon</label>
                                            <input type="file" name="icon" id="icon" accept="image/*,.svg"
                                                class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="icon_title">Title</label>
                                            <input type="text" name="title" id="icon_title" placeholder="Title"
                                                class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="icon_text">Text</label>
                                            <textarea name="text" id="icon_text" placeholder="Text" class="form-control"
                                                required></textarea>
                                        </div>

                                        <button type="submit" name="add_ad_icon" class="btn btn-primary">Add
                                            Icon</button>
                                    </form>

                                    <!-- ✅ All Ad Icons (بدون تغيير) -->
                                    <h3>All Ad Icons</h3>
                                    <div class="mb-4">
                                        <?php if ($ad_icons && $ad_icons->num_rows > 0): ?>
                                            <?php while ($icon = $ad_icons->fetch_assoc()): ?>
                                                <div class="d-inline-block text-center me-3 mb-3">
                                                    <img src="uploads/<?= htmlspecialchars($icon['icon'] ?? '') ?>" width="50"
                                                        class="img-thumbnail">
                                                    <div><?= htmlspecialchars($icon['title'] ?? '') ?></div>
                                                    <small><?= htmlspecialchars($icon['text'] ?? '') ?></small>
                                                    <a href="?delete_ad_icon=<?= intval($icon['id'] ?? 0) ?>"
                                                        class="btn btn-danger btn-sm mt-1">Delete Icon</a>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <p>No icons found.</p>
                                        <?php endif; ?>
                                    </div>
                                    <hr>

                                    <!-- ✅ Questions (بدون تغيير) -->


                                    <!-- Questions -->

                                </div>


                            </div>
                            <div id="analytics"> <!-- About Cards -->

                                <!-- About Slider -->
                                <h2>About Slider</h2>
                                <form method="POST" enctype="multipart/form-data" class="mb-3">
                                    <div class="mb-3">
                                        <input type="file" name="image" class="form-control" required>
                                    </div>
                                    <button name="add_about_slider" class="btn btn-primary">Add About
                                        Slider</button>
                                </form>
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
                                        <input type="text" name="link" placeholder="Link" class="form-control" required>
                                    </div>
                                    <button name="add_about_card" class="btn btn-primary">Add Card</button>
                                </form>
                                <div class="mb-4">
                                    <?php while ($row = $about_cards->fetch_assoc()): ?>
                                        <div class="card d-inline-block text-center me-3 mb-3" style="width: 150px;">
                                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                <a href="?delete_about_card=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm">Delete</a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <hr>
                            </div>

                            <div id="like">
                                <h2>Why Choose Us</h2>
                                <form method="POST" enctype="multipart/form-data" class="mb-3">
                                    <div class="mb-3">
                                        <input type="text" name="question" placeholder="Question" class="form-control"
                                            required>
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
                                            <img src="uploads/<?= htmlspecialchars($row['icon']) ?>" class="card-img-top"
                                                width="50">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($row['title']) ?></h6>
                                                <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                                                <a href="?delete_service=<?= intval($row['id']) ?>"
                                                    class="btn btn-danger btn-sm">Delete</a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
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
                modeToggle = body.querySelector(".mode-toggle");
            sidebar = body.querySelector("nav");
            sidebarToggle = body.querySelector(".sidebar-toggle");
            let getMode = localStorage.getItem("mode");
            if (getMode && getMode === "dark") {
                body.classList.toggle("dark");
            }
            let getStatus = localStorage.getItem("status");
            if (getStatus && getStatus === "close") {
                sidebar.classList.toggle("close");
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
            })
        </script>
    </main>



    <script>
        const links = document.querySelectorAll('.nav-links a');
        const sections = document.querySelectorAll('.content > div');

        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                // إزالة الـ active من الكل
                sections.forEach(sec => sec.classList.remove('active'));

                // إضافة الـ active للديف المطلوب
                const targetId = link.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');

                // حفظ الاختيار في localStorage
                localStorage.setItem('activeSection', targetId);
            });
        });

        // عند التحميل: اقرأ من localStorage
        const savedSection = localStorage.getItem('activeSection') || 'dashboard';
        document.getElementById(savedSection).classList.add('active');
    </script>


</body>

</html>