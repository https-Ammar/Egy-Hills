<?php
session_start();
include 'db.php';

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
    <link rel="icon" type="image/x-icon" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script
        src="https://www.rj-investments.co.uk/wp-content/themes/rj-investments/assets/js/min/jquery.min.js?ver=2.2.4"></script>
</head>

<body>
    <?php include 'header.php'; ?>


    <main class="main">
        <div class="modal fade" id="visitorModal" tabindex="-1" aria-labelledby="visitorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="visitorModalLabel">Enter Your Information</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $message; ?>
                        <form id="visitorForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="visitor_name" class="form-label">Name:</label>
                                <input type="text" id="visitor_name" name="visitor_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="visitor_phone" class="form-label">Phone Number:</label>
                                <input type="text" id="visitor_phone" name="visitor_phone" class="form-control"
                                    required>
                            </div>

                            <button type="submit" name="submit_visitor"
                                class="btn btn-primary send_info">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <section class="site-banner site-banner--home">
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
                        <h2 class="site-banner__subtitle">Exceptional Contemporary Living</h2>
                    </div>
                </div>

            </div>
        </section>

        <section class="about-section">
            <div class="container">



                <?php while ($row = $about_cards->fetch_assoc()): ?>
                    <div class="row clearfix">
                        <div class="content-column col-md-6 col-sm-12" data-aos="fade-right">
                            <div class="inner-column">
                                <div class="sec-title">
                                    <div class="title">About Us</div>

                                    <h2><?= htmlspecialchars($row['title']) ?></h2>
                                </div>
                                <div class="text">

                                    <?= htmlspecialchars($row['description']) ?>
                                </div>
                                <div class="email">
                                    Request a Quote: <span class="theme_color">EGY-HILLS@gmail.com</span>
                                </div>
                                <a href="<?php echo $row['link']; ?>" class="theme-btn btn-style-three">Read More</a>

                            </div>
                        </div>

                        <!-- Image Column -->
                        <div class="image-column col-md-6 col-sm-12" data-aos="fade-left">
                            <div class="inner-column" data-wow-delay="0ms" data-wow-duration="1500ms">
                                <div class="image">
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="About EGY-HILLS">
                                    <div class="overlay-box">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>


            </div>
        </section>

        <section class="property-highlights-section">
            <div class="container">
                <div class="row align-items-center mb-5">
                    <div class="col-md-8" data-aos="fade-right">
                        <h1 class="section-title">Property Highlights</h1>
                    </div>
                    <div class="col-md-4" data-aos="fade-left">
                        <p class="lead">
                            Discover the key features that make our properties stand out — from breathtaking views to
                            modern amenities designed for comfort and elegance.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <?php while ($row = $highlights->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="300">
                            <div class="highlight-card text-center"
                                style="background-image: url(uploads/<?= htmlspecialchars($row['image']) ?>);">
                                <div class="card_text_blur text-center">
                                    <h2 class="h4 mb-3"><?= htmlspecialchars($row['title']) ?></h2>
                                    <p class="mb-0"><?= htmlspecialchars($row['description']) ?></p>
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
                                <h6 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h6>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center"></p>
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
                        <a class="mt-addons-video-popup-vimeo-youtube" href="https://www.youtube.com/watch?v=' . $video_id . '">
                            <img decoding="async" class="mt-addons-video-buton-image"
                                src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                        </a>
                    ';
                        } else {
                            echo '<p>Invalid YouTube URL</p>';
                        }
                    } elseif (strpos($video['url'], 'vimeo.com') !== false) {
                        $video_id = substr(parse_url($video['url'], PHP_URL_PATH), 1);
                        echo '
                    <a class="mt-addons-video-popup-vimeo-youtube" href="https://vimeo.com/' . $video_id . '">
                        <img decoding="async" class="mt-addons-video-buton-image"
                            src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                    </a>
                ';
                    } else {
                        echo '
                    <a class="mt-addons-video-popup-vimeo-youtube" href="' . htmlspecialchars($video['url']) . '">
                        <img decoding="async" class="mt-addons-video-buton-image"
                            src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
                    </a>
                ';
                    }
                } else {
                    echo '<p>No video available.</p>';
                }
                ?>
            </div>
        </section>

        <section class="about-section bg_color mt-5">
            <div class="container">
                <?php while ($ad = $ads->fetch_assoc()): ?>
                    <div class="row clearfix">
                        <div class="content-column col-md-6 col-sm-12" data-aos="fade-right">
                            <div class="inner-column">
                                <div class="property-info">
                                    <h2 class="property-title mb-4" data-aos="fade-up" data-aos-delay="100">
                                        <?= htmlspecialchars($ad['title']) ?>
                                    </h2>
                                    <p class="mb-4" data-aos="fade-up" data-aos-delay="200">
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
                                                    <small class="text-muted"><?= $title ?></small>
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
                <div class="row">
                    <div class="col-12 mb-4" data-aos="fade-right" data-aos-delay="100">
                        <div class="col-md-12">
                            <h2 class="section-title">Discover Your Dream Home</h2>
                        </div>
                        <div class="col-md-6">
                            <p>Browse our exclusive collection of high-end properties, carefully selected to match your
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
                                                    <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?></h3>
                                                </div>
                                                <div class="property-card-features">
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
                                                        <?= (int) $row['beds'] ?> Beds
                                                    </div>
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
                                                        <?= (int) $row['baths'] ?> Baths
                                                    </div>
                                                    <div class="property-card-feature">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="lucide lucide-square">
                                                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                        </svg>
                                                        <?= htmlspecialchars($row['size']) ?>
                                                    </div>
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
                                <h2 class="fw-bold mb-3 mt-3">Plan and Room<br>Dimensions</h2>

                                <div class="mb-3">
                                    <button class="tab-btn active" onclick="showFloor(1)">FLOOR 1</button>
                                    <button class="tab-btn" onclick="showFloor(2)">FLOOR 2</button>
                                    <button class="tab-btn" onclick="showFloor(3)">FLOOR 3</button>
                                </div>

                                <p class="text-muted"><?= htmlspecialchars($row['title']) ?></p>

                                <div class="mt-addons-tab-content-v2">
                                    <?= nl2br(htmlspecialchars($row['description'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No floor plans available.</p>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="status-message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

            </section>



            <div class="row mb-4" data-aos="fade-right" data-aos-delay="100">
                <div class="col-md-12">
                    <h2 class="section-title">Our Services & Speed</h2>
                </div>
                <div class="col-md-6">
                    <p>Experience fast, reliable, and professional property services tailored to meet your needs with
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
                        <p>Over 15 years of expertise helping clients find the ideal property — fast and efficiently.
                        </p>
                        <div class="stats-number">24h</div>
                    </div>
                    <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="400">
                        <p>Extensive listings updated daily. From budget-friendly homes to premium estates, we have it
                            all.</p>
                        <div class="stats-number">250+</div>
                    </div>
                    <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="500">
                        <p>Client satisfaction is our priority — delivering seamless service and unmatched speed every
                            time.</p>
                        <div class="stats-number">99%</div>
                    </div>
                    <div class="col-md-3 info-box" data-aos="fade-up" data-aos-delay="600">
                        <p>Work with dedicated agents known for their responsiveness, market insight, and efficiency.
                        </p>
                        <div class="stats-number">150+</div>
                    </div>
                </div>

            </section>



            <!--  -->
            <section class="py-3 py-md-5">
                <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-md-12">
                        <h2 class="section-title">Why Choose Us?</h2>
                    </div>
                    <div class="col-md-6">
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
                                                <h5 class="me-2 fw-bold"><?= htmlspecialchars($row['question']) ?></h5>
                                                <span class="icon"><i class="bi bi-shield-check"></i></span>
                                            </div>
                                            <p class="text-muted"><?= htmlspecialchars($row['answer']) ?></p>
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
                        <h2 class="section-title">Our Real Estate Services</h2>
                    </div>
                    <div class="col-md-6">
                        <p>We provide tailored real estate services that cover all your needs — with speed, integrity,
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
                                    <h5 class="fw-bold"><?= htmlspecialchars($row['title']) ?></h5>
                                    <p class="text-muted"><?= htmlspecialchars($row['description']) ?></p>
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
    <script>
        const img3d = document.getElementById('img3d');
        let rotateX = 0, rotateY = 0, scale = 1;
        const maxScale = 2;
        const updateTransform = () => {
            img3d.style.transform = `rotateX(${-rotateX}deg) rotateY(${rotateY}deg) scale(${scale})`;
        };
        window.addEventListener('mousemove', e => {
            const { innerWidth, innerHeight } = window;
            const xMid = innerWidth / 2, yMid = innerHeight / 2;
            rotateY = ((e.clientX - xMid) / xMid) * 10;
            rotateX = ((e.clientY - yMid) / yMid) * 10;
            updateTransform();
        });
        window.addEventListener('mouseleave', () => {
            rotateX = rotateY = 0;
            updateTransform();
        });
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            const zoomLimit = window.innerHeight * 0.8;
            const progress = Math.min(scrollY / zoomLimit, 1);
            scale = 1 + progress * (maxScale - 1);
            updateTransform();
        });
        document.addEventListener('DOMContentLoaded', () => {
            const myModal = new bootstrap.Modal(document.getElementById('visitorModal'));
            myModal.show();
            const visitorForm = document.getElementById('visitorForm');
            visitorForm.addEventListener('submit', () => {
                localStorage.setItem('visitorRegistered', 'true');
            });
        });
    </script>
    <section id="footer"></section>
    <script src="./assets/script/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        button.btn.btn-primary.send_info {
            width: 100%;
            padding: 15px;
            border-radius: 30px;
            background: black;
            border: navajowhite;
        }

        .row.feature-box.aos-init.aos-animate {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
</body>

</html>