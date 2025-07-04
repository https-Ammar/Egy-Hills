<?php
include 'db.php';
$result = $conn->query("SELECT * FROM projects");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-translate>Featured Properties</title>
    <link rel="icon" href="../img/logo.jpeg" type="image/png">

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

    <section class="site-banner site-banner--bg site-banner--page" style="background-image:url(../img/Projects.jpg);">
        <div class="site-banner__txt section section--medium txt-center post-styles" data-aos="fade-down">
            <h1 class="site-banner__title">
                <a href="#" data-translate>Projects</a> / <a href="#" data-translate>Home</a>
            </h1>
            <h2 class="site-banner__subtitle" data-translate>Homes that move you</h2>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container pt-5">
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-md-12">
                    <h2 class="section-title" data-translate>Featured Properties</h2>
                </div>
                <div class="col-md-6">
                    <p data-translate>Explore our handpicked selection of premium properties designed to elevate your
                        living experience.</p>
                </div>
            </div>

            <button class="btn btn-dark w-100 d-md-none mb-3" onclick="toggleFilter()" data-translate>Show/Hide
                Filters</button>

            <form class="row g-3 mb-4 d-none d-md-flex" id="filter-form" data-aos="zoom-in">
                <div class="col-md-3">
                    <label for="min_price" class="form-label Max Price" data-translate>Min Price</label>
                    <input type="number" class="form-control" name="min_price" id="min_price" placeholder="e.g. 50000">
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label Max Price" data-translate>Max Price</label>
                    <input type="number" class="form-control" name="max_price" id="max_price" placeholder="e.g. 300000">
                </div>
                <div class="col-md-3">
                    <label for="rooms" class="form-label Max Price" data-translate>Rooms</label>
                    <input type="number" class="form-control" name="rooms" id="rooms" placeholder="e.g. 3">
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label Max Price" data-translate>Location</label>
                    <input type="text" class="form-control" name="location" id="location" placeholder="e.g. Cairo">
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary w-100" data-translate>Filter</button>
                </div>
            </form>

            <script>
                function toggleFilter() {
                    const form = document.getElementById('filter-form');
                    form.classList.toggle('d-none');
                }
            </script>

            <div id="projects-container" class="no-rtl">
                <div class="row mb-4" data-aos="fade-up">
                    <div class="col-md-12">
                        <h2 class="section-title" data-translate>Featured Properties</h2>
                    </div>
                </div>

                <div class="row">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up">
                                <a href="project_details.php?id=<?= (int) $row['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('/Egy-Hills/uploads/<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
                                        </div>
                                        <div class="property-card-content">
                                            <p class="property-card-location"><?= htmlspecialchars($row['location']) ?></p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['title']) ?></h3>
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?>
                                                    <sub>EGP</sub>
                                                </h3>
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
                                                    <span data-translate><?= (int) $row['beds'] ?> Beds</span>
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

                                                    <span data-translate> <?= (int) $row['baths'] ?> Baths</span>
                                                </div>
                                                <div class="property-card-feature">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-square">
                                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                    </svg>
                                                    <span data-translate><?= htmlspecialchars($row['size']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p data-translate>No projects found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include './footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="../script/app.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filter-form');
            const container = document.getElementById('projects-container');
            if (!form || !container) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                fetch(`filter_projects.php?${params}`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        AOS.refresh();
                    })
                    .catch(err => console.error(err));
            });
            form.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => {
                    form.dispatchEvent(new Event('submit'));
                });
            });
        });
    </script>
</body>

</html>