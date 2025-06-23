<?php
include 'db.php';

$about_slider = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$team_cards = $conn->query("SELECT * FROM about_team_cards");
$director_card = $conn->query("SELECT * FROM about_director_card LIMIT 1")->fetch_assoc();
$initiatives = $conn->query("SELECT * FROM about_initiatives");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php include './header.php'; ?>
    <?php include './loging.php'; ?>

    <?php while ($row = $about_slider->fetch_assoc()): ?>
        <section class="site-banner site-banner--bg site-banner--page"
            style="background-image:url(/Egy-Hills/uploads/<?= $row['image'] ?>);" data-aos="zoom-in"
            data-aos-duration="1000">
            <div class="site-banner__txt section section--medium txt-center post-styles">
                <h1 class="site-banner__title"><a href="#" data-translate>About</a> / <a href="# " data-translate>Home</a>
                </h1>
                <h2 class="site-banner__subtitle" data-translate>Homes that move you</h2>
            </div>
        </section>
    <?php endwhile; ?>

    <section class="about-section mt-5">
        <div class="container">
            <?php while ($row = $about_cards->fetch_assoc()): ?>
                <div class="row clearfix mb-5">
                    <div class="content-column col-md-6 col-sm-12" data-aos="fade-right" data-aos-duration="1000">
                        <div class="inner-column">
                            <div class="sec-title m-0">
                                <di data-translate class="title">About Us</di>
                                <h2 data-translate><?= $row['title'] ?></h2>
                            </div>
                            <div class="text" data-translate><?= $row['description'] ?></div>
                            <div class="email">Request a Quote: <span class="theme_color">EGY-HILLS@gmail.com</span></div>
                            <a href="#" class="theme-btn btn-style-three" data-translate>Read More</a>
                        </div>
                    </div>
                    <div class="image-column col-md-6 col-sm-12" data-aos="fade-left" data-aos-duration="1000">
                        <div class="inner-column">
                            <div class="image">
                                <img src="/Egy-Hills/uploads/<?= $row['image'] ?>" alt="About EGY-HILLS">
                                <div class="overlay-box">
                                    <div class="year-box">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="timeline-container " id="timeline-container" data-aos="fade-up" data-aos-duration="1000">
        <div class="text  mb-4">
            <h2 data-translate>Our Journey Through the Years</h2>
            <p data-translate>From our humble beginnings to remarkable milestones, each year has shaped our legacy of
                excellence and innovation.</p>

        </div>
        <div class="timeline" id="timeline">
            <?php $i = 0;
            while ($card = $team_cards->fetch_assoc()):
                $i++; ?>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <?php if ($i % 2 == 0): ?>
                        <p class="title"><?= htmlspecialchars($card['name']) ?></p>
                        <div class="year">
                            <hr>
                            <div class="rool_sec">
                                <div class="about-sec"
                                    style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($card['image']) ?>);">
                                    <p><?= htmlspecialchars($card['phone']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="year">
                            <div class="rool_sec">
                                <div class="about-sec"
                                    style="background-image: url(/Egy-Hills/uploads/<?= htmlspecialchars($card['image']) ?>);">
                                    <p><?= htmlspecialchars($card['phone']) ?></p>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <p class="title" data-translate><?= htmlspecialchars($card['name']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <?php if ($director_card): ?>
        <section class="about-section">
            <div class="container">
                <div class="row clearfix align-items-center">
                    <div class="image-column col-md-6 col-sm-12" data-aos="fade-right" data-aos-duration="1000">
                        <div class="inner-column">
                            <div class="image">
                                <img src="/Egy-Hills/uploads/<?= htmlspecialchars($director_card['image']) ?>"
                                    alt="Director Image">
                            </div>
                        </div>
                    </div>
                    <div class="content-column col-md-6 col-sm-12" data-aos="fade-left" data-aos-duration="1000">
                        <div class="inner-column">
                            <div class="sec-title">
                                <div class="title" data-translate>Director</div>
                                <h2 data-translate><?= htmlspecialchars($director_card['title']) ?></h2>
                            </div>
                            <div class="text" data-translate><?= nl2br(htmlspecialchars($director_card['text'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section>
        <div class="container py-5">
            <?php $i = 0;
            while ($initiative = $initiatives->fetch_assoc()):
                $i++;
                $even = $i % 2 === 0; ?>
                <div class="row align-items-center mb-5 flex-md-row<?= $even ? '-reverse' : '' ?>" data-aos="fade-up"
                    data-aos-delay="<?= $i * 100 ?>">
                    <div class="col-md-6">
                        <h2 class="csr-title" data-translate>CSR <span class="csr-subtitle" data-translate>Full at Badya
                                University</span></h2>
                        <p class="mt-3"><?= $i ?> - <?= $i + 1 ?></p>
                        <h4 class="fw-bold" data-translate><?= htmlspecialchars($initiative['title']) ?></h4>
                        <p class="text-muted fw-semibold" data-translate><?= htmlspecialchars($initiative['name']) ?></p>
                        <?php if (!empty($initiative['link'])): ?>
                            <div class="d-flex align-items-center mt-4">
                                <a href="<?= htmlspecialchars($initiative['link']) ?>" class="me-4">
                                    <div class="learn-more"></div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="csr-img bg"
                            style="background-image: url('/Egy-Hills/uploads/<?= htmlspecialchars($initiative['image']) ?>');">
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="spacing-small bg-dark no-rtl " data-aos="zoom-in-up" data-aos-duration="1000">
        <ul class="usp-list section section--large grid">
            <li class="usp-list__item grid__col grid__col--4 txt-center">
                <a class="usp-list__link" href="/investors">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/package.svg" alt="">
                    </div>
                    <h6 data-translate>Investment Options</h6>
                    <p class="usp-list__desc" data-translate>Get More</p>
                </a>
            </li>
            <li class="usp-list__item grid__col grid__col--4 txt-center">
                <a class="usp-list__link" href="/property-type/accomodation">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/accommodation.svg" alt="">
                    </div>
                    <h6 data-translate>Accommodation</h6>
                    <p class="usp-list__desc" data-translate>Future Tenants</p>
                </a>
            </li>
            <li class="usp-list__item grid__col grid__col--4 txt-center">
                <a class="usp-list__link" href="/pandora-homes/">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/development.svg" alt="">
                    </div>
                    <h6 data-translate>Land Development</h6>
                    <p class="usp-list__desc" data-translate>Meet Pandora Homes</p>
                </a>
            </li>
        </ul>
    </section>

    <?php include './footer.php'; ?>

    <script src="../script/app.js"></script>
    <script>
        const timeline = document.getElementById('timeline');
        const container = document.getElementById('timeline-container');
        const items = timeline.innerHTML;
        timeline.innerHTML += items + items;

        let pos = 0, speed = 0, ticking = false;
        const singleWidth = timeline.scrollWidth / 3;

        window.addEventListener('wheel', e => {
            const visible = container.getBoundingClientRect();
            if (visible.top < window.innerHeight && visible.bottom > 0) {
                speed += e.deltaY * 0.05;
                if (!ticking) requestAnimationFrame(update);
                ticking = true;
            }
        });

        let isDown = false, startX, lastPos;

        container.addEventListener('mousedown', e => {
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX;
            lastPos = pos;
        });

        container.addEventListener('mousemove', e => {
            if (!isDown) return;
            const dx = e.pageX - startX;
            pos = lastPos + dx;
            loopPosition();
            timeline.style.transform = `translateX(${pos}px)`;
        });

        window.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });

        function loopPosition() {
            if (pos < -singleWidth) pos += singleWidth;
            if (pos > 0) pos -= singleWidth;
        }

        function update() {
            pos -= speed;
            speed *= 0.8;
            loopPosition();
            timeline.style.transform = `translateX(${pos}px)`;
            if (Math.abs(speed) > 0.1) requestAnimationFrame(update);
            else ticking = false;
        }
    </script>
</body>

</html>