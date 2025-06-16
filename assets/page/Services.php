<?php
include 'db.php';

// Fetch services data
$services = $conn->query("SELECT * FROM services");
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">

</head>

<body>
    <?php include './header.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page"
        style="background-image:url(uploads/1750093639_20250524083242.webp);">

        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title"><a href="#">About</a> / <a href="#">Home</a></h1>
            <h2 class="site-banner__subtitle">Homes that move you</h2>
        </div>

    </section>

    <main class="container">


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
                                <?php if (!empty($row['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Service Image">
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold"><?= htmlspecialchars($row['title'] ?? '') ?></h5>
                            <p class="text-muted">
                                <?= htmlspecialchars($row['description'] ?? '') ?>
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


</body>

</html>