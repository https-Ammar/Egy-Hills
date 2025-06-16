<?php
include 'db.php';

$about_slider = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">

</head>

<body
    class="home wp-singular page-template page-template-templates page-template-home page-template-templateshome-php page page-id-59 wp-theme-rj-investments no-touch no-js site-scroll--inactive logo-dark">

    <?php include './header.php'; ?>


    <?php while ($row = $about_slider->fetch_assoc()): ?>


        <section class="site-banner site-banner--bg site-banner--page"
            style="background-image:url(./uploads/<?php echo $row['image']; ?>);">

            <div class="site-banner__txt section section--medium txt-center post-styles">
                <h1 class="site-banner__title"><a href="#">About</a> / <a href="#">Home</a></h1>
                <h2 class="site-banner__subtitle">Homes that move you</h2>
            </div>

        </section>

    <?php endwhile; ?>



    <section class="about-section mt-5">
        <div class="container">


            <?php while ($row = $about_cards->fetch_assoc()): ?>


                <div class="row clearfix">

                    <!-- Content Column -->
                    <div class="content-column col-md-6 col-sm-12 aos-init aos-animate" data-aos="fade-right">
                        <div class="inner-column">
                            <div class="sec-title">
                                <div class="title">About Us</div>
                                <h2><?php echo $row['title']; ?></h2>
                            </div>
                            <div class="text">
                                <?php echo $row['description']; ?>
                            </div>
                            <div class="email">
                                Request a Quote: <span class="theme_color">EGY-HILLS@gmail.com</span>
                            </div>
                            <a href="#" class="theme-btn btn-style-three">Read More</a>
                        </div>
                    </div>

                    <!-- Image Column -->
                    <div class="image-column col-md-6 col-sm-12 aos-init aos-animate" data-aos="fade-left">
                        <div class="inner-column" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="image">
                                <img src="./uploads/<?php echo $row['image']; ?>" alt="About EGY-HILLS">
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

    <section class="spacing-small bg-dark">
        <ul class="usp-list section section--large grid">



            <li class="usp-list__item grid__col grid__col--4 grid__col--tb2-12 grid__col--m-12 txt-center">


                <a class="usp-list__link" href="/investors">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/package.svg"
                            alt="RJ Investments Packages">
                    </div>

                    <h6>Investment Options</h6>
                    <p class="usp-list__desc">Get More</p>
                </a>
            </li>



            <li class="usp-list__item grid__col grid__col--4 grid__col--tb2-12 grid__col--m-12 txt-center">


                <a class="usp-list__link" href="/property-type/accomodation">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/accommodation.svg"
                            alt="RJ Investments Accommodation">
                    </div>

                    <h6>Accommodation</h6>
                    <p class="usp-list__desc">Future Tenants</p>
                </a>
            </li>



            <li class="usp-list__item grid__col grid__col--4 grid__col--tb2-12 grid__col--m-12 txt-center">


                <a class="usp-list__link" href="/pandora-homes/">
                    <div class="usp-list__icon-outer">
                        <img class="usp-list__icon"
                            src="https://www.rj-investments.co.uk/wp-content/uploads/2018/02/development.svg"
                            alt="RJ Investments Land Development">
                    </div>

                    <h6>Land Development</h6>
                    <p class="usp-list__desc">Meet Pandora Homes</p>
                </a>
            </li>


        </ul>
    </section>



    <section id="footer"></section>




</body>

</html>