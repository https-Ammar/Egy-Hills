<?php
include 'db.php';

// Fetch services data
$services = $conn->query("SELECT * FROM services");
$announcements = $conn->query("SELECT * FROM new_services WHERE type='announcement' ORDER BY created_at DESC LIMIT 1");
$newServices = $conn->query("SELECT * FROM new_services WHERE type='service' ORDER BY created_at DESC");
$recentPosts = $conn->query("SELECT * FROM new_services WHERE type='service' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">

</head>

<body>
    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <!-- Banner -->
    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/services.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title">
                <a href="#" data-translate>Services</a> /
                <a href="#" data-translate>Home</a>
            </h1>
            <h2 class="site-banner__subtitle" data-translate>Homes that move you</h2>
        </div>
    </section>

    <!-- Main Services -->
    <main class="container">
        <section class="py-5">
            <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="col-md-12">
                    <h2 class="section-title" data-translate>Our Real Estate Services</h2>
                </div>
                <div class="col-md-6">
                    <p data-translate>We provide tailored real estate services that cover all your needs — with speed,
                        integrity, and professionalism.</p>
                </div>
            </div>


        </section>
    </main>

    <!-- Featured & Recent Articles -->
    <div class="container container-flex">
        <main role="main">
            <!-- Latest Announcement -->
            <?php while ($row = $announcements->fetch_assoc()): ?>
                <article class="article-featured">
                    <h2 class="article-title" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                    <div class="div card-img-top"
                        style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);"></div>
                    <p class="article-info"><?= date('Y-m-d', strtotime($row['created_at'])) ?></p>
                    <p class="article-body" data-translate><?= htmlspecialchars($row['description']) ?></p>
                    <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more" data-translate>CONTINUE
                        READING</a>
                </article>
            <?php endwhile; ?>

            <!-- Services Posts -->
            <?php while ($row = $newServices->fetch_assoc()): ?>
                <article class="article-recent">
                    <div class="article-recent-main">
                        <h2 class="article-title" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                        <p class="article-body" data-translate><?= htmlspecialchars($row['description']) ?></p>
                        <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more" data-translate>CONTINUE
                            READING</a>
                    </div>
                    <div class="article-recent-secondary">
                        <div class="services_img"
                            style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);"></div>
                        <p class="article-info"><?= date('Y-m-d', strtotime($row['created_at'])) ?></p>
                    </div>
                </article>
            <?php endwhile; ?>
        </main>

        <!-- Sidebar -->
        <aside class="sidebar aside-none">
            <div class="sidebar-widget">
                <h2 class="widget-title" data-translate>RECENT POSTS</h2>
            </div>
            <div class="sidebar-widget">
                <?php while ($row = $recentPosts->fetch_assoc()): ?>
                    <article class="article-recent d-block">
                        <div class="article-recent-main">
                            <h2 class="article-title" data-translate><?= htmlspecialchars($row['title']) ?></h2>
                        </div>
                        <div class="article-recent-secondary w-100">
                            <div class="services_img"
                                style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);">
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </aside>
    </div>

    <?php include './footer.php'; ?>

    <script src="../script/app.js"></script>
</body>

</html>