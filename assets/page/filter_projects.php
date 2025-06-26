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
                                    <h3 class="property-card-title"><?= htmlspecialchars($row['price']) ?> <sub>EGP</sub></h3>
                                </div>
                                <div class="property-card-features">
                                    <div class="property-card-feature">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-bed">
                                            <path d="M2 9V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"></path>
                                            <path d="M2 11v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9"></path>
                                            <path d="M2 14h20"></path>
                                            <path d="M4 9h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2Z">
                                            </path>
                                        </svg>
                                        <span data-translate><?= (int) $row['beds'] ?> Beds</span>
                                    </div>
                                    <div class="property-card-feature">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-bath">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-square">
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
        </div>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>
    <p class="mt-4">Total Projects Found: <?= $result->num_rows ?></p>
</div>