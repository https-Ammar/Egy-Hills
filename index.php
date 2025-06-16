<?php
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

if (!$projects) {
    die("Query Error: " . $conn->error);
}

if (isset($_POST['submit_visitor'])) {
    $name = trim($_POST['visitor_name']);
    $phone = trim($_POST['visitor_phone']);
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;

    if (!empty($name) && !empty($phone) && $project_id > 0) {
        $check = $conn->prepare("SELECT id FROM projects WHERE id = ?");
        $check->bind_param("i", $project_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO visitors (name, phone, project_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $phone, $project_id);

            if ($stmt->execute()) {
                echo "<p style='color:green;'></p>";
            } else {
                echo "<p style='color:red;'>Database error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>Invalid project ID.</p>";
        }

        $check->close();
    } else {
        echo "<p style='color:red;'>Invalid input data.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGY-HILLS | Best Real Estate Properties for Sale and Rent in Egypt</title>
    <meta name="description"
        content="Discover the best real estate deals in Egypt with EGY-HILLS. Apartments, villas, and lands for sale and rent at competitive prices." />
    <meta name="keywords"
        content="real estate Egypt, apartments for sale, villas for rent, lands for sale, Cairo properties" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="canonical" href="https://www.egy-hills.com/" />
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Open Graph -->
    <meta property="og:title" content="EGY-HILLS | Best Real Estate Properties in Egypt" />
    <meta property="og:description"
        content="Find top apartments, villas, and lands for sale or rent in Egypt with EGY-HILLS." />
    <meta property="og:url" content="https://www.egy-hills.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://www.egy-hills.com/images/cover.jpg" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="EGY-HILLS | Best Real Estate Properties in Egypt" />
    <meta name="twitter:description"
        content="Find top apartments, villas, and lands for sale or rent in Egypt with EGY-HILLS." />
    <meta name="twitter:image" content="https://www.egy-hills.com/images/cover.jpg" />
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="icon" type="image/x-icon" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script type="text/javascript"
        src="https://www.rj-investments.co.uk/wp-content/themes/rj-investments/assets/js/min/jquery.min.js?ver=2.2.4"
        id="jquery-js"></script>

</head>




<body
    class="home wp-singular page-template page-template-templates page-template-home page-template-templateshome-php page page-id-59 wp-theme-rj-investments no-touch no-js site-scroll--inactive logo-dark">




    <?php include 'header.php'; ?>


    <section class="site-banner site-banner--home">
        <div class="site-banner__inner">

            <?php while ($row = $sliders->fetch_assoc()): ?>

                <div class="site-banner__img" style="background-image:url(uploads/<?= htmlspecialchars($row['image']) ?>);">
                </div>
            <?php endwhile; ?>


            <div class="site-banner__txt-outer section section--large grid">
                <div class="site-banner__txt grid__col grid__col--6">
                    <h1 class="site-banner__title">Luxury
                        <br>
                        House Shares
                    </h1>
                    <h2 class="site-banner__subtitle">Exceptional Contemporary Living</h2>
                </div>
            </div>

        </div>
    </section>


    <main class="container">

        <section class="about-section">
            <div class="container">



                <?php while ($row = $about_cards->fetch_assoc()): ?>




                    <div class="row clearfix">

                        <!-- Content Column -->
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
                                        <div class="year-box">
                                            <span class="number">5</span> Years <br> of Excellence <br> in Real Estate
                                        </div>
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
                    <!-- Title Column -->
                    <div class="col-md-8" data-aos="fade-right">
                        <h1 class="section-title">Property Highlights</h1>
                    </div>

                    <!-- Description Column -->
                    <div class="col-md-4" data-aos="fade-left">
                        <p class="lead">
                            Discover the key features that make our properties stand out — from breathtaking views to
                            modern amenities designed for comfort and elegance.
                        </p>
                    </div>
                </div>

                <div class="row">

                    <?php while ($row = $highlights->fetch_assoc()): ?>

                        <!-- Highlight Card 3 -->
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
                    <!-- Feature 1 -->
                    <div class="col-md-4 mb-4 text-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="mb-3">
                            <img decoding="async" alt="Cooling & Heating System" style="max-width: 120px;"
                                class="mt-image-list" src="./assets/img/skyhaus-aerothermal-icon.svg">
                        </div>
                        <h6 class="fw-bold">Central Cooling & Heating System</h6>
                    </div>

                    <!-- Feature 2 -->
                    <div class="col-md-4 mb-4 text-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="mb-3">
                            <img decoding="async" alt="Underfloor Heating" style="max-width: 120px;"
                                class="mt-image-list" src="./assets/img/skyhaus-underfloor-heating-icon.svg">
                        </div>
                        <h6 class="fw-bold">Underfloor Heating Technology</h6>
                    </div>

                    <!-- Feature 3 -->
                    <div class="col-md-4 mb-4 text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="mb-3">
                            <img decoding="async" alt="Smart Home System" style="max-width: 120px;"
                                class="mt-image-list" src="./assets/img/skyhaus-icon-domotics-1.svg">
                        </div>
                        <h6 class="fw-bold">Integrated Smart Home System</h6>
                    </div>
                </div>
            </div>
        </section>





    </main>

    <section class="mt-5 mb-5" data-aos="zoom-in">
        <div class="video_phot text-center">
            <a class="mt-addons-video-popup-vimeo-youtube" href="https://www.youtube.com/watch?v=rqrAhBmimCY">
                <img decoding="async" class="mt-addons-video-buton-image"
                    src="https://skyhaus.modeltheme.com/wp-content/uploads/2023/05/play-button.svg" alt="Watch Video">
            </a>
        </div>


    </section>



    <main class="container">


        <section class="about-section bg_color mt-5 ">
            <div class="container">

                <?php while ($ad = $ads->fetch_assoc()): ?>



                    <div class="row clearfix">

                        <!-- Content Column -->
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
                                                    // إذا كان مسار صورة
                                                    if (preg_match('/\.(png|jpg|jpeg|svg|gif)$/i', $iconValue)) {
                                                        echo '<img decoding="async" alt="' . $title . '" style="max-width:50px;" class="mt-image-list" src="uploads/' . $iconValue . '">';
                                                    } else {
                                                        // إذا كان كلاس أيقونة
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

                        <!-- Image Column -->
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

        <section class="card_project" data-aos="fade-up">
            <div class="row">
                <div class="col-12 mb-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="col-md-12">
                        <h2 class="section-title">Discover Your Dream Home</h2>
                    </div>
                    <div class="col-md-6">
                        <p>Browse our exclusive collection of high-end properties, thoughtfully selected to match your
                            lifestyle, comfort, and aspirations.</p>
                    </div>
                </div>



                <?php if ($projects->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($row = $projects->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <a href="./assets//page/project_details.php?id=<?= (int) $row['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('/uploads/<?= !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
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
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
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
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
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
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
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
            <div class="row align-items-center">

                <!-- Floor Plan Image -->
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <img src="./assets/img/skyhaus-floor-img-min-scaled.jpg" alt="Floor Plan" class="img-fluid rounded">
                </div>

                <!-- Description and Tabs -->
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <h2 class="fw-bold mb-3 mt-3">Plan and Room<br>Dimensions</h2>

                    <!-- Tabs -->
                    <div class="mb-3">
                        <button class="tab-btn active" onclick="showFloor(1)">FLOOR 1</button>
                        <button class="tab-btn" onclick="showFloor(2)">FLOOR 2</button>
                        <button class="tab-btn" onclick="showFloor(3)">FLOOR 3</button>
                    </div>

                    <!-- Description -->
                    <p class="text-muted">
                        A property description is made up of 2 parts: key features and property description.
                        The key features section is your opportunity to tell potential tenants about the key selling
                        points of your property in a bullet point format.
                    </p>

                    <!-- Room Dimensions -->
                    <div class="mt-addons-tab-content-v2">
                        Bedroom 1&nbsp; ------------- <strong> 39 Sq Ft</strong><br>
                        Bedroom 2 ------------- <strong> 38 Sq Ft</strong><br>
                        Kitchen&nbsp; &nbsp; &nbsp; &nbsp; ------------- <strong> 49 Sq Ft</strong><br>
                        Gym Area&nbsp; &nbsp;------------- <strong> 55 Sq Ft</strong><br>
                        Bathroom&nbsp; &nbsp;------------- <strong> 65 Sq Ft</strong><br>
                    </div>
                </div>

            </div>
        </section>

        <section class="service" data-aos="fade-up">

            <!-- Title and Intro -->
            <div class="row mb-4" data-aos="fade-right" data-aos-delay="100">
                <div class="col-md-12">
                    <h2 class="section-title">Our Services & Speed</h2>
                </div>
                <div class="col-md-6">
                    <p>Experience fast, reliable, and professional property services tailored to meet your needs with
                        precision and care.</p>
                </div>
            </div>

            <!-- Cover Image -->
            <div class="row" data-aos="zoom-in" data-aos-delay="200">
                <div class="cover_img_product mt-5 rounded-3"
                    style="background: url('') center/cover no-repeat; height: 50vh;">
                    <img src="./assets/img/wiktor-karkocha-resize.png" alt="">
                </div>
            </div>

            <!-- Statistics -->
            <div class="row text-center pt-5">
                <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="300">
                    <p>Over 15 years of expertise helping clients find the ideal property — fast and efficiently.</p>
                    <div class="stats-number">24h</div>
                </div>
                <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="400">
                    <p>Extensive listings updated daily. From budget-friendly homes to premium estates, we’ve got it
                        all.</p>
                    <div class="stats-number">250+</div>
                </div>
                <div class="col-md-3 info-box border-end-custom" data-aos="fade-up" data-aos-delay="500">
                    <p>Client satisfaction is our priority — delivering seamless service and unmatched speed every time.
                    </p>
                    <div class="stats-number">99%</div>
                </div>
                <div class="col-md-3 info-box" data-aos="fade-up" data-aos-delay="600">
                    <p>Work with dedicated agents known for their responsiveness, market insight, and efficiency.</p>
                    <div class="stats-number">150+</div>
                </div>
            </div>

        </section>





        <section class="py-3 py-md-5">
            <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="col-md-12">
                    <h2 class="section-title">Why Choose Us?</h2>
                </div>
                <div class="col-md-6">
                    <p>We deliver seamless real estate services backed by years of experience, client trust, and a deep
                        understanding of the market.</p>
                </div>
            </div>
            <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
                <div class="col-12 col-lg-6 col-xl-7" data-aos="fade-right" data-aos-delay="200">
                    <div class="row justify-content-xl-center_">
                        <div class="col-12 col-xl-11">
                            <?php while ($row = $questions->fetch_assoc()): ?>
                                <div class="mb-5" data-aos="fade-up" data-aos-delay="400">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="me-2 fw-bold"><?= htmlspecialchars($row['question']) ?></h5>
                                        <span class="icon"><i class="bi bi-shield-check"></i></span>
                                    </div>
                                    <p class="text-muted"><?= htmlspecialchars($row['answer']) ?></p>
                                    <hr>
                                </div>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
                                <?php endif; ?>
                            <?php endwhile; ?>
                            <!-- Trust & Transparency -->
                        </div>
                    </div>
                </div>
                <!-- Image -->
                <div class="col-12 col-lg-6 col-xl-5" data-aos="fade-left" data-aos-delay="300">
                    <img class="img-fluid rounded" loading="lazy" src="./assets/img/about-img-1 copy.webp"
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
                    <p>We provide tailored real estate services that cover all your needs — with speed, integrity, and
                        professionalism.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Buying -->
                <?php while ($row = $services->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card p-4 service-card h-100">
                            <div class="icon-box">
                                <img src="uploads/<?= htmlspecialchars($row['icon']) ?>" alt=" Buying">
                            </div>
                            <h5 class="fw-bold"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="text-muted">
                                <?= htmlspecialchars($row['description']) ?>
                            </p>
                            <div class="arrow-btn">
                                <span>↗</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>




            </div>
        </section>
    </main>





    <section id="footer"></section>



</body>

</html>