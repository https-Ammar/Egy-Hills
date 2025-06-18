<?php
include 'db.php';

// عرض جميع المشاريع بدون فلترة عند الفتح
$result = $conn->query("SELECT * FROM projects");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Featured Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/main.css">
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


    <section class="py-5 bg-white">
        <div class="container pt-5">

            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="section-title">Featured Properties</h2>
                </div>
                <div class="col-md-6">
                    <p>Explore our handpicked selection of premium properties designed to elevate your living
                        experience.</p>
                </div>
            </div>


            <form class="row g-3 mb-4" id="filter-form">
                <div class="col-md-3">
                    <label for="min_price" class="form-label">Min Price</label>
                    <input type="number" class="form-control" name="min_price" id="min_price" placeholder="e.g. 50000">
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label">Max Price</label>
                    <input type="number" class="form-control" name="max_price" id="max_price" placeholder="e.g. 300000">
                </div>
                <div class="col-md-2">
                    <label for="rooms" class="form-label">Rooms</label>
                    <input type="number" class="form-control" name="rooms" id="rooms" placeholder="e.g. 3">
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" id="location" placeholder="e.g. Cairo">
                </div>
                <div class="col-md-1 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <div id="projects-container">
                <div class="row">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <a href="project_details.php?id=<?= (int) $row['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('<?= !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
                                        </div>
                                        <div class="property-card-content">
                                            <p class="property-card-location"><?= htmlspecialchars($row['location']) ?></p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['title']) ?></h3>
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?></h3>
                                            </div>
                                            <div class="property-card-features">
                                                <div class="property-card-feature"><?= (int) $row['beds'] ?> Beds</div>
                                                <div class="property-card-feature"><?= (int) $row['baths'] ?> Baths</div>
                                                <div class="property-card-feature"><?= htmlspecialchars($row['size']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No projects found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section id="footer"></section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/app.js"></script>
    <script src="../script/footer.js"></script>
    <script>
        const form = document.getElementById('filter-form');
        const container = document.getElementById('projects-container');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            fetch(`filter_projects.php?${params}`)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(err => console.error(err));
        });

        // تشغيل التصفية التلقائية عند الكتابة
        form.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                form.dispatchEvent(new Event('submit'));
            });
        });
    </script>
</body>

</html>