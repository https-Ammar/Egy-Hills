<?php
include 'db.php';

$min_price = isset($_GET['min_price']) ? (int) $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : 0;
$rooms = isset($_GET['rooms']) ? (int) $_GET['rooms'] : 0;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$conditions = [];
$params = [];
$types = '';

if ($min_price > 0) {
    $conditions[] = 'price >= ?';
    $params[] = $min_price;
    $types .= 'i';
}
if ($max_price > 0) {
    $conditions[] = 'price <= ?';
    $params[] = $max_price;
    $types .= 'i';
}
if ($rooms > 0) {
    $conditions[] = 'beds = ?';
    $params[] = $rooms;
    $types .= 'i';
}
if ($location !== '') {
    $conditions[] = 'location LIKE ?';
    $params[] = '%' . $location . '%';
    $types .= 's';
}

$sql = 'SELECT * FROM projects';
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div id="projects-container">
    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="project_details.php?id=<?= (int) $row['id'] ?>">
                        <div class="property-card">
                            <div class="cover_card"
                                style="background-image: url('/Egy-Hills/uploads/<?= !empty($row['image']) ? '' . htmlspecialchars($row['image']) : 'placeholder.jpg' ?>');">
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
        </div>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>
    <p class="mt-4">Total Projects Found: <?= $result->num_rows ?></p>
</div>