<?php
session_start();
include 'db.php';

$ip = $_SERVER['REMOTE_ADDR'];
$conn->query("INSERT INTO site_visits (ip_address) VALUES ('$ip')");

$total_visits_result = $conn->query("SELECT COUNT(*) as total FROM site_visits");
$total_visits = 0;
if ($total_visits_result && $row = $total_visits_result->fetch_assoc()) {
    $total_visits = $row['total'];
}

$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$ad_icons = $conn->query("SELECT * FROM ad_icons");
$projects = $conn->query("SELECT * FROM projects");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");
$plan_and_room = $conn->query("SELECT * FROM plan_and_room ORDER BY id DESC");
$property_highlights = $conn->query("SELECT * FROM property_highlights ORDER BY id DESC");

if (!$projects) {
    http_response_code(500);
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_visitor'])) {
        $name = trim($_POST['visitor_name'] ?? '');
        $phone = trim($_POST['visitor_phone'] ?? '');
        if ($name !== '' && $phone !== '') {
            $stmt = $conn->prepare("INSERT INTO visitors (project_id, name, phone) VALUES (NULL, ?, ?)");
            $stmt->bind_param("ss", $name, $phone);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Saved successfully";
            }
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    if (isset($_POST['add_property_highlight'])) {
        $title = trim($_POST['title'] ?? '');
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $name = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $name)) {
                $image = $name;
            }
        }
        if ($title !== '' && $image !== '') {
            $stmt = $conn->prepare("INSERT INTO property_highlights (image, title) VALUES (?, ?)");
            $stmt->bind_param("ss", $image, $title);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Added successfully";
            }
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

if (isset($_GET['delete_property_highlight'])) {
    $id = intval($_GET['delete_property_highlight']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM property_highlights WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Deleted successfully";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_SESSION['success'])) {
    $message = "<p style='color:green;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}

$visitors = $conn->query("SELECT name, phone FROM visitors ORDER BY id DESC");
$property_highlights = $conn->query("SELECT * FROM property_highlights ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGY-HILLS | Best Real Estate Properties for Sale and Rent in Egypt</title>
    <link rel="icon" href="./assets/img/logo.jpeg" type="image/png">
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>


<body>
    <?php include './assets/page/header.php'; ?>
    <?php include './assets/page/loging.php'; ?>



    <main class="main">
        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="visitorModalLabel" data-translate>Enter Your Information</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $message; ?>
                        <form id="visitorForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="visitor_name" class="form-label" data-translate>Name:</label>
                                <input type="text" id="visitor_name" name="visitor_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="visitor_phone" class="form-label" data-translate>Phone Number:</label>
                                <input type="text" id="visitor_phone" name="visitor_phone" class="form-control"
                                    required>
                            </div>

                            <button type="submit" name="submit_visitor" class="btn btn-primary send_info"
                                data-translate>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <section class="site-banner site-banner--home no-rtl">
            <div class="site-banner__inner">

                <?php while ($row = $sliders->fetch_assoc()): ?>

                    <div class="site-banner__img"
                        style="background-image:url(uploads/<?= htmlspecialchars($row['image']) ?>);">
                    </div>
                <?php endwhile; ?>


                <div class="site-banner__txt-outer section section--large grid">
                    <div class="site-banner__txt grid__col grid__col--6">
                        <h1 class="site-banner__title">Egy-Hills
                            <br>
                            real estate
                        </h1>
                        <h2 class="site-banner__subtitle" data-translate>Exceptional Contemporary Living</h2>
                    </div>
                </div>

            </div>
        </section>

        <section class="about-section ">
            <div class="container">


                <?php $i = 0; ?>
                <?php while ($row = $about_cards->fetch_assoc()): ?>
                    <?php $i++; ?>
                    <div class="row clearfix d-flex align-items-center">
                        <?php if ($i % 2 == 0): ?>


                            <div class="image-column col-md-6 col-sm-12 order-md-1" data-aos="fade-right">
                                <div class="inner-column" data-wow-delay="0ms" data-wow-duration="1500ms">
                                    <div class="image">
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="About EGY-HILLS">
                                        <div class="overlay-box"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="content-column col-md-6 col-sm-12 order-md-2" data-aos="fade-left">
                                <div class="inner-column">
                                    <div class="sec-title">
                                        <div class="title" data-translate>About Us</div>
                                        <h2 data-translate><?= htmlspecialchars($row['title']) ?></h2>
                                    </div>
                                    <div class="text" data-translate>
                                        <?= html_entity_decode($row['description']) ?>
                                    </div>

                                    <div class="email">
                                        Request a Quote: <span class="theme_color">EGY-HILLS@gmail.com</span>
                                    </div>
                                    <a href="<?= $row['link']; ?>" class="theme-btn btn-style-three">Read More</a>
                                </div>
                            </div>

                        <?php else: ?>

                            <!-- المحتوى يمين، الصورة شمال -->
                            <div class="content-column col-md-6 col-sm-12 order-md-1" data-aos="fade-right">
                                <div class="inner-column">
                                    <div class="sec-title">
                                        <div class="title" data-translate>About Us</div>
                                        <h2 data-translate><?= htmlspecialchars($row['title']) ?></h2>
                                    </div>
                                    <div class="text" data-translate>
                                        <?= htmlspecialchars($row['description']) ?>
                                    </div>
                                    <div class="email">
                                        Request a Quote: <span class="theme_color">EGY-HILLS@gmail.com</span>
                                    </div>
                                    <a href="<?= $row['link']; ?>" class="theme-btn btn-style-three">Read More</a>
                                </div>
                            </div>

                            <div class="image-column col-md-6 col-sm-12 order-md-2" data-aos="fade-left">
                                <div class="inner-column" data-wow-delay="0ms" data-wow-duration="1500ms">
                                    <div class="image">
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="About EGY-HILLS">
                                        <div class="overlay-box"></div>
                                    </div>
                                </div>
                            </div>


                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

            </div>
        </section>

        <section class="property-highlights-section">
            <div class="container">
                <div class="row align-items-center mb-5">
                    <div class="col-md-8" data-aos="fade-right">
                        <h1 class="section-title" data-translate>Property Highlights</h1>
                    </div>
                    <div class="col-md-4" data-aos="fade-left">
                        <p class="lead" data-translate>
                            Discover the key features that make our properties stand out — from breathtaking views to
                            modern amenities designed for comfort and elegance.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <?php while ($row = $highlights->fetch_assoc()): ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-4" data-aos="zoom-in" data-aos-delay="300">
                            <div class="highlight-card text-center"
                                style="background-image: url(uploads/<?= htmlspecialchars($row['image']) ?>);">
                                <div class="card_text_blur text-center">
                                    <h2 class="h4 mb-3" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                                    <p class="mb-0" data-translate><?= htmlspecialchars($row['description']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>


            </div>
        </section>

        <section class="features-section py-5" data-aos="fade-up">
            <div class="container">
                <div class="row">
                    <?php if ($property_highlights && $property_highlights->num_rows > 0): ?>
                        <?php while ($row = $property_highlights->fetch_assoc()): ?>
                            <div class="col-md-4 mb-4 text-center" data-aos="fade-up" data-aos-delay="100">
                                <div class="mb-3">
                                    <img decoding="async" alt="Feature Image" style="max-width: 120px;" class="mt-image-list"
                                        src="uploads/<?php echo htmlspecialchars($row['image']); ?>">
                                </div>
                                <h6 class="fw-bold" data-translate><?php echo htmlspecialchars($row['title']); ?></h6>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center" data-translate></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>


        <section class="mt-5 mb-5" data-aos="zoom-in">
            <div class="video_phot text-center">
                <?php
                $video = $conn->query("SELECT url FROM videos ORDER BY id DESC LIMIT 1")->fetch_assoc();

                if (!empty($video['url'])) {
                    if (strpos($video['url'], 'youtube.com') !== false || strpos($video['url'], 'youtu.be') !== false) {
                        $video_id = '';
                        if (strpos($video['url'], 'youtube.com') !== false) {
                            parse_str(parse_url($video['url'], PHP_URL_QUERY), $params);
                            $video_id = $params['v'] ?? '';
                        } elseif (strpos($video['url'], 'youtu.be') !== false) {
                            $video_id = substr(parse_url($video['url'], PHP_URL_PATH), 1);
                        }

                        if (!empty($video_id)) {
                            echo '
                    <div class="video-popup-container">
                        <div class="video-thumbnail" onclick="openFullscreenPopup(\'https://www.youtube.com/embed/' . $video_id . '\')">
                            <img decoding="async" class="mt-addons-video-buton-image"
                                src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                        </div>
                    </div>
                    ';
                        } else {
                            echo '<p>Invalid YouTube URL</p>';
                        }
                    } elseif (strpos($video['url'], 'vimeo.com') !== false) {
                        $video_id = substr(parse_url($video['url'], PHP_URL_PATH), 1);
                        echo '
                <div class="video-popup-container">
                    <div class="video-thumbnail" onclick="openFullscreenPopup(\'https://player.vimeo.com/video/' . $video_id . '\')">
                        <img decoding="async" class="mt-addons-video-buton-image"
                            src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                    </div>
                </div>
                ';
                    } else {
                        echo '
                <div class="video-popup-container">
                    <div class="video-thumbnail" onclick="openFullscreenPopup(\'' . htmlspecialchars($video['url']) . '\')">
                        <img decoding="async" class="mt-addons-video-buton-image"
                            src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                    </div>
                </div>
                ';
                    }
                } else {
                    echo '<p>No video available.</p>';
                }
                ?>
            </div>
        </section>

        <!-- Popup HTML -->
        <div id="fullscreenPopup" class="fullscreen-popup">
            <div class="fullscreen-popup-content">
                <span class="fullscreen-close-btn" onclick="closeFullscreenPopup()">&times;</span>
                <div class="video-container">
                    <iframe id="fullscreenVideoFrame" width="100%" height="100%" frameborder="0"
                        allowfullscreen></iframe>
                </div>
            </div>
        </div>

        <style>
            .fullscreen-popup {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.9);
                z-index: 9999;
                align-items: center;
                justify-content: center;
            }

            .fullscreen-popup-content {
                position: relative;
                width: 100%;
                height: 100%;
            }

            .video-container {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .fullscreen-close-btn {
                position: absolute;
                top: 20px;
                right: 30px;
                color: white;
                font-size: 40px;
                font-weight: bold;
                cursor: pointer;
                z-index: 10000;
                background: rgba(0, 0, 0, 0.5);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }

            .fullscreen-close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .video-thumbnail {
                cursor: pointer;
                display: inline-block;
                position: relative;
            }

            .video-thumbnail:hover img {
                opacity: 0.8;
            }

            /* Responsive video sizing */
            @media (min-aspect-ratio: 16/9) {
                #fullscreenVideoFrame {
                    width: 90vw;
                    height: calc(90vw * 9 / 16);
                }
            }

            @media (max-aspect-ratio: 16/9) {
                #fullscreenVideoFrame {
                    width: calc(90vh * 16 / 9);
                    height: 90vh;
                }
            }
        </style>

        <script>
            function openFullscreenPopup(videoUrl) {
                document.getElementById('fullscreenVideoFrame').src = videoUrl;
                document.getElementById('fullscreenPopup').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeFullscreenPopup() {
                document.getElementById('fullscreenPopup').style.display = 'none';
                document.getElementById('fullscreenVideoFrame').src = '';
                document.body.style.overflow = 'auto';
            }

            // Close popup when clicking outside the video
            document.getElementById('fullscreenPopup').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeFullscreenPopup();
                }
            });

            // Close with ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeFullscreenPopup();
                }
            });
        </script>

        <section class="about-section bg_color mt-5">
            <div class="container">
                <?php while ($ad = $ads->fetch_assoc()): ?>
                    <div class="row clearfix">
                        <div class="content-column col-md-6 col-sm-12" data-aos="fade-right">
                            <div class="inner-column">
                                <div class="property-info">
                                    <h2 class="property-title mb-4" data-aos="fade-up" data-aos-delay="100" data-translate>
                                        <?= htmlspecialchars($ad['title']) ?>
                                    </h2>
                                    <p class="mb-4" data-aos="fade-up" data-aos-delay="200" data-translate>
                                        <?= htmlspecialchars($ad['description']) ?>
                                    </p>
                                    <div class="row feature-box" data-aos="fade-up" data-aos-delay="400">
                                        <?php
                                        $icons = $conn->query("SELECT * FROM ad_icons WHERE ad_id = " . intval($ad['id']));
                                        while ($icon = $icons->fetch_assoc()):
                                            $iconValue = htmlspecialchars($icon['icon']);
                                            $title = htmlspecialchars($icon['title']);
                                            $text = htmlspecialchars($icon['text']);
                                            ?>
                                            <div class="col d-flex align-items-center gap-3">
                                                <div class="icon">
                                                    <?php
                                                    if (preg_match('/\.(png|jpg|jpeg|svg|gif)$/i', $iconValue)) {
                                                        echo '<img decoding="async" alt="' . $title . '" style="max-width:50px;" class="mt-image-list" src="uploads/' . $iconValue . '">';
                                                    } else {
                                                        echo '<i class="' . $iconValue . '" style="font-size: 40px;"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="block">
                                                    <small class="text-muted" data-translate><?= $title ?></small>
                                                    <div class="property-number"><?= $text ?></div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="image-column col-md-6 col-sm-12" data-aos="fade-left">
                            <div class="inner-column">
                                <div class="image">
                                    <img src="uploads/<?= htmlspecialchars($ad['image']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>



        <div class="container">
            <section class="card_project" data-aos="fade-up">
                <div class="row no-rtl">
                    <div class="col-12 mb-4" data-aos="fade-right" data-aos-delay="100">
                        <div class="col-md-12">
                            <h2 class="section-title" data-translate>Discover Your Dream Home</h2>
                        </div>
                        <div class="col-md-6">
                            <p data-translate>Browse our exclusive collection of high-end properties, carefully selected
                                to match your
                                lifestyle, comfort, and aspirations.</p>
                        </div>
                    </div>

                    <?php if ($projects->num_rows > 0): ?>
                        <div class="row">
                            <?php
                            $count = 0;
                            while ($row = $projects->fetch_assoc()):
                                if ($count >= 3)
                                    break;
                                $count++;
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <a href="./assets/page/project_details.php?id=<?= (int) $row['id'] ?>">
                                        <div class="property-card">
                                            <div class="cover_card"
                                                style="background-image: url('uploads/<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
                                            </div>
                                            <div class="property-card-content">
                                                <p class="property-card-location">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-map-pin">
                                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                        <circle cx="12" cy="10" r="3"></circle>
                                                    </svg>
                                                    <?= htmlspecialchars($row['location']) ?>
                                                </p>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h3 class="property-card-title"><?= htmlspecialchars($row['title']) ?></h3>
                                                    <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?>
                                                        <sub>EGP</sub>
                                                    </h3>
                                                </div>


                                                <div class="property-card-features">
                                                    <?php if (!empty($row['beds']) && (int) $row['beds'] > 0): ?>
                                                        <div class="property-card-feature">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-bed">
                                                                <path d="M2 9V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"></path>
                                                                <path d="M2 11v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9"></path>
                                                                <path d="M2 14h20"></path>
                                                                <path
                                                                    d="M4 9h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2Z">
                                                                </path>
                                                            </svg>
                                                            <span data-translate><?= (int) $row['beds'] ?> Beds</span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($row['baths']) && (int) $row['baths'] > 0): ?>
                                                        <div class="property-card-feature">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-bath">
                                                                <path
                                                                    d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5">
                                                                </path>
                                                                <line x1="10" x2="8" y1="5" y2="7"></line>
                                                                <line x1="2" x2="22" y1="12" y2="12"></line>
                                                                <line x1="7" x2="7" y1="19" y2="21"></line>
                                                                <line x1="17" x2="17" y1="19" y2="21"></line>
                                                            </svg>
                                                            <span data-translate><?= (int) $row['baths'] ?> Baths</span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($row['size'])): ?>
                                                        <div class="property-card-feature">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-square">
                                                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                            </svg>
                                                            <span data-translate><?= htmlspecialchars($row['size']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No projects found.</p>
                    <?php endif; ?>
                </div>
            </section>



            <section style="margin-bottom: 15vh;">

                <?php if ($plan_and_room && $plan_and_room->num_rows > 0): ?>
                    <?php while ($row = $plan_and_room->fetch_assoc()): ?>
                        <div class="row align-items-center">
                            <!-- Floor Plan Image -->
                            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Floor Plan"
                                    class="img-fluid rounded">
                            </div>

                            <!-- Description and Tabs -->
                            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                                <h2 class="fw-bold mb-3 mt-3" data-translate><?= htmlspecialchars($row['title']) ?></h2>

                                <div class="mb-3">
                                    <button class="tab-btn active" onclick="showFloor(1)">FLOOR 1</button>

                                </div>



                                <div class="mt-addons-tab-content-v2" data-translate>
                                    <?= nl2br(htmlspecialchars($row['description'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p data-translate>No floor plans available.</p>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="status-message" data-translate><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

            </section>



            <div class="row mb-4 no-rtl" data-aos="fade-right" data-aos-delay="100">
                <div class="col-md-12">
                    <h2 class="section-title" data-translate>Our Services & Speed</h2>
                </div>
                <div class="col-md-6">
                    <p data-translate>Experience fast, reliable, and professional property services tailored to meet
                        your needs with
                        precision and care.</p>
                </div>
            </div>

            <section class="service" data-aos="fade-up">


                <div class="row" data-aos="zoom-in" data-aos-delay="200">
                    <div class="col-12">
                        <div class="section-3d">
                            <img src="./assets/img/wiktor-karkocha-resize.png" alt="3D House" id="img3d">
                        </div>
                        <div class="cover_img_product mt-5 rounded-3"></div>
                    </div>
                </div>


                <div class="row text-center pt-5">
                    <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="300">
                        <p data-translate>Over 15 years of expertise helping clients find the ideal property — fast and
                            efficiently.
                        </p>
                        <div class="stats-number">24h</div>
                    </div>
                    <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="400">
                        <p data-translate>Extensive listings updated daily. From budget-friendly homes to premium
                            estates, we have it
                            all.</p>
                        <div class="stats-number">250+</div>
                    </div>
                    <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="500">
                        <p data-translate>Client satisfaction is our priority — delivering seamless service and
                            unmatched speed every
                            time.</p>
                        <div class="stats-number">99%</div>
                    </div>
                    <div class="col-md-3 info-box" data-aos="fade-up" data-aos-delay="600">
                        <p data-translate>Work with dedicated agents known for their responsiveness, market insight, and
                            efficiency.
                        </p>
                        <div class="stats-number">150+</div>
                    </div>

                </div>

            </section>


            <section class="py-3 py-md-5">
                <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-md-12">
                        <h2 class="section-title" data-translate>Why Choose Us?</h2>
                    </div>
                    <div class="col-md-6" data-translate>
                        <p>We deliver seamless real estate services backed by years of experience, client trust, and a
                            deep understanding of the market.</p>
                    </div>
                </div>

                <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
                    <div class="col-12 col-lg-6 col-xl-7" data-aos="fade-right" data-aos-delay="200">
                        <div class="row justify-content-xl-center">
                            <div class="col-12 col-xl-11">
                                <?php if ($questions && $questions->num_rows > 0): ?>
                                    <?php while ($row = $questions->fetch_assoc()): ?>
                                        <div class="mb-5" data-aos="fade-up" data-aos-delay="400">
                                            <div class="d-flex align-items-center mb-2">
                                                <h5 class="me-2 fw-bold" data-translate>
                                                    <?= htmlspecialchars($row['question']) ?>
                                                </h5>
                                                <span class="icon"><i class="bi bi-shield-check"></i></span>
                                            </div>
                                            <p class="text-muted" data-translate><?= htmlspecialchars($row['answer']) ?></p>
                                            <hr>
                                            <?php if (!empty($row['image'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200"
                                                    alt="Related Image">
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p>No questions available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-5" data-aos="fade-left" data-aos-delay="300">
                        <img class="img-fluid rounded" loading="lazy" src="./assets/img/about-img-1.webp"
                            alt="About Us">
                    </div>
                </div>
            </section>
            <section class="py-5">
                <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-md-12">
                        <h2 class="section-title" data-translate>Our Real Estate Services</h2>
                    </div>
                    <div class="col-md-6">
                        <p data-translate>We provide tailored real estate services that cover all your needs — with
                            speed, integrity,
                            and professionalism.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <?php if ($services && $services->num_rows > 0): ?>
                        <?php while ($row = $services->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                                <div class="card p-4 service-card h-100">
                                    <div class="icon-box">
                                        <img src="uploads/<?= htmlspecialchars($row['icon']) ?>"
                                            alt="<?= htmlspecialchars($row['title']) ?>">
                                    </div>
                                    <h5 class="fw-bold" data-translate><?= htmlspecialchars($row['title']) ?></h5>
                                    <p class="text-muted" data-translate><?= htmlspecialchars($row['description']) ?></p>
                                    <div class="arrow-btn">
                                        <span>↗</span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No services available.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include './assets/page/footer.php'; ?>
    <script>
        const paths = {
            home: "./index.php",
            about: "./assets/page/About.php",
            projects: "./assets/page/projects.php",
            services: "./assets/page/services.php",
            inquiry: "./assets/page/Inquiry.php",
            privacy: "./assets/page/privacy.php",
            contact: "./assets/page/contact.php"
        };

        document.querySelectorAll(".site-menu__links a").forEach(link => {
            const key = Object.keys(paths).find(k =>
                link.textContent.toLowerCase().includes(k) ||
                (k === "projects" && link.textContent.toLowerCase().includes("developments")) ||
                (k === "inquiry" && link.textContent.toLowerCase().includes("booking"))
            );
            if (key) link.href = paths[key];
        });
    </script>
    <script src="./assets/script/app.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            var visitorModal = new bootstrap.Modal(document.getElementById('visitorModal'));
            visitorModal.show();
        });


    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const logo = document.querySelector(".footer-logo img");
            if (logo) {
                logo.src = "./assets/img/main_logo.jpeg";
            }
        });
    </script>


</body>
</html>