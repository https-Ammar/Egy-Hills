<?php
include 'db.php';

$projects = $conn->query("SELECT * FROM projects");
if (!$projects) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style.css">
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

    <main>
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

                <?php if ($projects->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($row = $projects->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <a href="project_details.php?id=<?= (int) $row['id'] ?>">
                                    <div class="property-card">
                                        <div class="cover_card"
                                            style="background-image: url('<?= !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
                                        </div>
                                        <div class="property-card-content">
                                            <p class="property-card-location">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-map-pin">
                                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <?= htmlspecialchars($row['location']) ?>
                                            </p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['title']) ?></h3>
                                                <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?></h3>
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
                                                    <?= (int) $row['beds'] ?> Beds
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
                                                    <?= (int) $row['baths'] ?> Baths
                                                </div>
                                                <div class="property-card-feature">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-square">
                                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                    </svg>
                                                    <?= htmlspecialchars($row['size']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No projects found.</p>
                <?php endif; ?>

                <p class="mt-4">Total Projects Found: <?= $projects->num_rows ?></p>
            </div>
        </section>
    </main>

    <section id="footer"></section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/app.js"></script>
    <script src="../script/footer.js"></script>

</body>

</html>