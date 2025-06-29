<?php
include 'db.php';

$services = $conn->query("SELECT * FROM services");
$announcements = $conn->query("SELECT * FROM new_services WHERE type='announcement' ORDER BY created_at DESC LIMIT 1");
$newServices = $conn->query("SELECT * FROM new_services WHERE type='service' ORDER BY created_at DESC");
$recentPosts = $conn->query("SELECT * FROM new_services WHERE type='service' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Our Services</title>
    <link rel="icon" href="../img/logo.jpeg" type="image/png">

    <link rel="stylesheet" href="../css/page.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/services.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title">
                <a href="#" data-translate>Services</a> /
                <a href="#" data-translate>Home</a>
            </h1>
            <h2 class="site-banner__subtitle" data-translate>Homes that move you</h2>
        </div>
    </section>

    <main class="container my-5">
        <section class="py-3">
            <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="col-12">
                    <h2 class="section-title" data-translate>Our Real Estate Services</h2>
                </div>
                <div class="col-12 col-md-6">
                    <p data-translate>
                        We provide tailored real estate services that cover all your needs — with speed,
                        integrity, and professionalism.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <div class="container my-5">
        <div class="row">
            <main role="main" class="col-12 col-lg-12">
                <?php while ($row = $announcements->fetch_assoc()): ?>
                    <article class="article-featured">
                        <h2 class="article-title" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                        <div class="card-img-top mb-3 mt-3"
                            style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);"></div>
                        <p class="article-info"><?= date('Y-m-d', strtotime($row['created_at'])) ?></p>
                        <p class="article-body" data-translate><?= htmlspecialchars($row['description']) ?></p>
                        <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more" data-translate>
                            open link</a>
                    </article>
                <?php endwhile; ?>

                <?php while ($row = $newServices->fetch_assoc()): ?>
                    <div class="articles-container row">
                        <article class="article-recent col-12 col-md-12 gap-3">
                            <div class="article-recent-main flex-fill">
                                <h2 class="article-title" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                                <p class="article-body" data-translate><?= htmlspecialchars($row['description']) ?></p>
                                <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more"
                                    data-translate>open link</a>
                            </div>
                            <div class="article-recent-secondary" style="min-width:200px;">
                                <div class="services_img"
                                    style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);">
                                </div>
                                <p class="article-info mt-2 text-muted"><?= date('Y-m-d', strtotime($row['created_at'])) ?>
                                </p>
                            </div>
                        </article>
                    </div>

                <?php endwhile; ?>
            </main>


        </div>
    </div>

    <?php include './footer.php'; ?>

    <script src="../script/app.js"></script>
</body>

</html>