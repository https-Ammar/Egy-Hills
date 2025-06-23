<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Header and Navigation | Egy-Hills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">

    <!-- Popup and AOS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />

    <style>
        ul {
            list-style: none;
        }

        .example-1 {
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bolder;
            border-radius: 60px;
            padding: 20px;
            height: 70px;
            margin: 20px;
            position: fixed;
            left: 0;
        }

        .example-1 .icon-content {
            margin: 0 10px;
            position: relative;
            font-weight: bolder;
        }

        .example-1 .icon-content .tooltip {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            color: #000;
            padding: 6px 10px;
            border-radius: 5px;
            opacity: 0;
            visibility: hidden;
            font-size: 14px;
            transition: all 0.3s ease;
            font-weight: bolder;
        }

        .example-1 .icon-content .link {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: #fff;
            background-color: #414a55;
            transition: all 0.3s ease-in-out;
            font-weight: bolder;
        }

        .example-1 .icon-content .link:hover {
            box-shadow: 3px 2px 45px 0px rgb(0 0 0 / 12%);
        }

        .example-1 .icon-content .link svg {
            width: 30px;
            height: 30px;
            fill: #fff;
        }

        .property-card-content {
            color: #718096;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="site-header">
        <div class="d-flex align-items-center no-rtl"></div>

        <!-- Menu Icon -->
        <a class="menu-icon js-toggle-menu" href="#">
            <div class="menu-icon__txt">Menu</div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 40">
                <rect width="4" height="26" x="18" y="14" fill="#373f48" />
                <rect width="4" height="40" x="9" fill="#373f48" />
                <rect width="4" height="26" y="14" fill="#373f48" />
            </svg>
        </a>
    </header>

    <!-- Navigation -->
    <nav class="site-menu">
        <div class="site-menu__inner">

            <!-- Left Background -->
            <div class="site-menu__left"
                style="background-image: url(https://www.rj-investments.co.uk/wp-content/uploads/2018/02/Menu-1000x1200.jpg);">
            </div>

            <!-- Language Switcher -->
            <ul class="example-1">
                <li class="icon-content"><a href="#" aria-label="Telegram" data-social="telegram" class="link"
                        data-lang="en">En</a></li>
                <li class="icon-content"><a href="#" aria-label="Telegram" data-social="telegram" class="link"
                        data-lang="ar">Ar</a></li>
                <li class="icon-content"><a href="#" aria-label="Telegram" data-social="telegram" class="link"
                        data-lang="fr">Fr</a></li>
                <li class="icon-content"><a href="#" aria-label="Telegram" data-social="telegram" class="link"
                        data-lang="es">Es</a></li>
            </ul>

            <!-- Right Navigation -->
            <div class="site-menu__right">
                <ul class="site-menu__links">
                    <li><a href="/index.php" data-translate>Home</a></li>
                    <li><a href="./About.php" data-translate>About Us</a></li>
                    <li><a href="./Projects.php" data-translate>Projects / Developments</a></li>
                    <li><a href="./Services.php" data-translate>Services</a></li>
                    <li><a href="./Inquiry.php" data-translate>Booking / Inquiry</a></li>
                    <li><a href="./privacy.php" data-translate>Privacy Policy</a></li>
                    <li><a href="./Contact.php" data-translate>Contact Us</a></li>
                </ul>

                <!-- Social Icons -->
                <ul class="social social--menu">
                    <li><a href="https://www.facebook.com/" target="_blank"></a></li>
                    <li><a href="https://twitter.com/" target="_blank"></a></li>
                    <li><a href="https://www.instagram.com/" target="_blank"></a></li>
                    <li><a href="https://uk.linkedin.com/" target="_blank"></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script
        src="https://www.rj-investments.co.uk/wp-content/themes/rj-investments/assets/js/min/all.min.js?ver=1533632804"></script>
    <script
        src="https://www.rj-investments.co.uk/wp-content/themes/rj-investments/assets/js/min/ajax.min.js?ver=1533632804"></script>

    <script>
        var ajax_data = {
            ajax_admin: "https://www.rj-investments.co.uk/wp-admin/admin-ajax.php",
            security: "718b83ca75"
        };

        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-7T5YMSC4Y8');

        jQuery(document).ready(function () {
            jQuery(".mt-addons-video-popup-vimeo-youtube, .mt-addons-video-popup-vimeo-video").magnificPopup({
                type: "iframe",
                disableOn: 700,
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false
            });
        });

        AOS.init();
    </script>

</body>

</html>