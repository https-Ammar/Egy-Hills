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
                                    <img src="/Egy-Hills/uploads/<?= htmlspecialchars($row['image']) ?>" alt="Service Image">
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


    <!-- container starts -->
    <div class="container container-flex">
        <main role="main">

            <?php
            $announcements = $conn->query("SELECT * FROM new_services WHERE type='announcement' ORDER BY created_at DESC LIMIT 1");
            while ($row = $announcements->fetch_assoc()):
                ?>

                <article class="article-featured">
                    <h2 class="article-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                    <img src="/Egy-Hills/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top"
                        alt="Announcement Image">
                    <p class="article-info"> <?php echo date('Y-m-d', strtotime($row['created_at'])); ?></p>
                    <p class="article-body"><?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="<?php echo htmlspecialchars($row['link']); ?>" class="article-read-more">CONTINUE READING</a>
                </article>
            <?php endwhile; ?>




            <?php
            $services = $conn->query("SELECT * FROM new_services WHERE type='service' ORDER BY created_at DESC");
            while ($row = $services->fetch_assoc()):
                ?>

                <!--  -->
                <article class="article-recent">
                    <div class="article-recent-main">
                        <h2 class="article-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p class="article-body"><?php echo htmlspecialchars($row['description']); ?></p>

                        <a href="<?php echo htmlspecialchars($row['link']); ?>" class="article-read-more">CONTINUE
                            READING</a>
                    </div>
                    <div class="article-recent-secondary">
                        <img src="/Egy-Hills/uploads/<?php echo htmlspecialchars($row['image']); ?>"
                            alt="two dumplings on a wood plate with chopsticks" class="article-image">
                        <p class="article-info"> <?php echo date('Y-m-d', strtotime($row['created_at'])); ?></p>
                    </div>
                </article>



            <?php endwhile; ?>





        </main>

        <aside class="sidebar">

            <div class="sidebar-widget">
                <h2 class="widget-title">ABOUT ME</h2>
                <img src="https://raw.githubusercontent.com/kevin-powell/reponsive-web-design-bootcamp/master/Module%202-%20A%20simple%20life/img/about-me.jpg"
                    alt="john doe" class="widget-image">
                <p class="widget-body">I find life better, and I'm happier, when things are nice and simple.</p>
            </div>

            <div class="sidebar-widget">
                <h2 class="widget-title">RECENT POSTS</h2>
                <div class="widget-recent-post">
                    <h3 class="widget-recent-post-title">Keeping cooking simple</h3>
                    <img src="https://raw.githubusercontent.com/kevin-powell/reponsive-web-design-bootcamp/master/Module%202-%20A%20simple%20life/img/food.jpg"
                        alt="two dumplings on a wood plate with chopsticks" class="widget-image">
                </div>
                <div class="widget-recent-post">
                    <h3 class="widget-recent-post-title">Simplicity and work</h3>
                    <img src="https://raw.githubusercontent.com/kevin-powell/reponsive-web-design-bootcamp/master/Module%202-%20A%20simple%20life/img/work.jpg"
                        alt="a chair white chair at a white desk on a white wall" class="widget-image">
                </div>
                <div class="widget-recent-post">
                    <h3 class="widget-recent-post-title">Simple decorations</h3>
                    <img src="https://raw.githubusercontent.com/kevin-powell/reponsive-web-design-bootcamp/master/Module%202-%20A%20simple%20life/img/deco.jpg"
                        alt="a green plant in a clear, round vase with water in it" class="widget-image">
                </div>
            </div>

        </aside>

    </div>
    <!-- container ends -->


    <style>
        /* Typography */
        @import url('https://fonts.googleapis.com/css2?family=Lora&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Lora&family=Ubuntu:wght@300;400;700&display=swap');



        .subtitle {
            font-size: 0.85rem;
            font-weight: 700;
            margin: 0;
            color: #1792d2;
            letter-spacing: 0.05em;
            font-family: 'Ubuntu Bold', sans-serif;
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



        .container-flex {

            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            margin-bottom: 1em;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
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


        img {
            max-width: 100%;
            display: block;
        }

        main {
            max-width: 75%;
        }

        .article-body {
            width: 100%;
            text-align: justify;
        }

        .sidebar {
            max-width: 23%;
        }


        @media (max-width:1050px) {
            .container-flex {
                flex-direction: column;
            }

            .site-title,
            .subtitle {
                width: 100%;
            }

            main {
                max-width: 100%;
            }

            .sidebar {
                max-width: 100%;
            }

        }

        @media (max-width: 500px) {
            nav ul {
                display: flex;
                flex-direction: column;
            }

            nav li {
                margin: 0.5em 0;
            }
        }

        /* articles */
        .article-featured {
            border-bottom: #707070 1px solid;
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
    </style>


</body>

</html>