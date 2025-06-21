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

    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <?php include './header.php'; ?>

    <!-- Banner -->
    <section class="site-banner site-banner--bg site-banner--page"
        style="background-image:url(uploads/1750093639_20250524083242.webp);">
        <div class="site-banner__txt section section--medium txt-center post-styles">
            <h1 class="site-banner__title"><a href="#">About</a> / <a href="#">Home</a></h1>
            <h2 class="site-banner__subtitle">Homes that move you</h2>
        </div>
    </section>

    <!-- Main Services -->
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
                <?php while ($row = $services->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card p-4 service-card h-100">
                            <div class="icon-box">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>" alt="Service Image">
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($row['description']) ?></p>
                            <div class="arrow-btn"><span>↗</span></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <!-- Featured & Recent Articles -->
    <div class="container container-flex">
        <main role="main">
            <!-- Latest Announcement -->
            <?php while ($row = $announcements->fetch_assoc()): ?>
                <article class="article-featured">
                    <h2 class="article-title"><?= htmlspecialchars($row['title']) ?></h2>
                    <div class=" div card-img-top"
                        style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);">
                    </div>
                    <p class="article-info"><?= date('Y-m-d', strtotime($row['created_at'])) ?></p>
                    <p class="article-body"><?= htmlspecialchars($row['description']) ?></p>
                    <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more">CONTINUE READING</a>
                </article>
            <?php endwhile; ?>

            <!-- Services Posts -->
            <?php while ($row = $newServices->fetch_assoc()): ?>
                <article class="article-recent">
                    <div class="article-recent-main">
                        <h2 class="article-title"><?= htmlspecialchars($row['title']) ?></h2>
                        <p class="article-body"><?= htmlspecialchars($row['description']) ?></p>
                        <a href="<?= htmlspecialchars($row['link']) ?>" class="article-read-more">CONTINUE READING</a>
                    </div>
                    <div class="article-recent-secondary">
                        <div class="services_img"
                            style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>);">
                        </div>
                        <p class="article-info"><?= date('Y-m-d', strtotime($row['created_at'])) ?></p>
                    </div>
                </article>
            <?php endwhile; ?>
        </main>

        <!-- Sidebar -->
        <aside class="sidebar aside-none">
            <div class="sidebar-widget">
                <h2 class="widget-title">RECENT POSTS</h2>
            </div>
            <div class="sidebar-widget">
                <?php while ($row = $recentPosts->fetch_assoc()): ?>
                    <article class="article-recent d-block">
                        <div class="article-recent-main">
                            <h2 class="article-title"><?= htmlspecialchars($row['title']) ?></h2>
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
</body>

</html>
<style>
    /* Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Lora&family=Ubuntu:wght@300;400;700&display=swap');

    /* General Typography */
    body {
        font-family: 'Ubuntu', sans-serif;
    }

    .subtitle {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0;
        color: #1792d2;
        letter-spacing: 0.05em;
    }

    .article-title {
        font-size: 1.5rem;
    }

    .article-read-more,
    .article-info {
        font-size: .875rem;
    }

    .article-read-more {
        color: #1792d2;
        text-decoration: none;
        font-weight: 700;
    }

    .article-read-more:hover,
    .article-read-more:focus {
        color: #143774;
        text-decoration: underline;
    }

    .article-info {
        margin: 2em 0;
    }

    /* Layout Containers */
    .container-flex {
        margin: 0 auto 1em;
        display: flex;
        justify-content: space-between;
    }

    main {
        max-width: 75%;
    }

    .sidebar {
        max-width: 23%;
    }

    /* Navbar */
    nav ul {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    nav li {
        margin: 0 1em;
    }

    nav a {
        text-decoration: none;
        color: #707070;
        font-weight: 700;
        padding: 0.25em 0;
    }

    /* Images */
    img {
        max-width: 100%;
        display: block;
    }

    .div.card-img-top {
        width: 100%;
        height: 55vh;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
    }

    .services_img {
        width: 100%;
        height: 25vh;
        background-size: cover;
        background-position: center;
        border-radius: 5px;
    }

    /* Articles */
    .article-body {
        width: 100%;
        text-align: justify;
    }

    .article-featured {
        border-bottom: 1px solid #707070;
        padding-bottom: 2em;
        margin-bottom: 2em;
    }

    .article-recent {
        display: flex;
        flex-direction: column;
        margin-bottom: 2em;
    }

    .article-recent-main {
        order: 2;
    }

    .article-recent-secondary {
        order: 1;
    }

    /* Responsive Styles */
    @media (min-width: 675px) {
        .article-recent {
            flex-direction: row;
            justify-content: space-between;
        }

        .article-recent-main {
            width: 68%;
        }

        .article-recent-secondary {
            width: 30%;
        }
    }

    @media (max-width: 1050px) {
        .container-flex {
            flex-direction: column;
        }

        .site-title,
        .subtitle,
        main,
        .sidebar {
            width: 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 500px) {
        nav ul {
            flex-direction: column;
        }

        nav li {
            margin: 0.5em 0;
        }
    }

    @media screen and (max-width:992px) {
        aside.sidebar.aside-none {
            display: none !important;
        }
    }
</style>