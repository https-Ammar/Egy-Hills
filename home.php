<?php
include 'db.php';

// === Fetch Data ===
$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");

// === Save Visitor ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_visitor'])) {
    $name = trim($_POST['visitor_name'] ?? '');
    $phone = trim($_POST['visitor_phone'] ?? '');

    if ($name && $phone) {
        $stmt = $conn->prepare("INSERT INTO visitors (name, phone) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $phone);
        $stmt->execute();
        $successMessage = "Thank you! Your inquiry has been received.";
    } else {
        $errorMessage = "Please fill in both name and phone fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>

    <h2>Booking / Inquiry</h2>
    <?php if (isset($successMessage)): ?>
        <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
    <?php elseif (isset($errorMessage)): ?>
        <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="visitor_name" placeholder="Your Name" required>
        <input type="text" name="visitor_phone" placeholder="Phone Number" required>
        <button type="submit" name="submit_visitor">Submit</button>
    </form>

    <hr>
    <h1>Main Slider</h1>
    <?php while ($row = $sliders->fetch_assoc()): ?>
        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
        <?php endif; ?>
    <?php endwhile; ?>

    <hr>
    <h1>About Us</h1>
    <h2>About Sliders</h2>
    <?php while ($row = $about_sliders->fetch_assoc()): ?>
        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
        <?php endif; ?>
    <?php endwhile; ?>

    <h2>About Cards</h2>
    <?php while ($row = $about_cards->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
        <?php endif; ?>
    <?php endwhile; ?>

    <hr>
    <h1>Property Highlights</h1>
    <?php while ($row = $highlights->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
        <?php endif; ?>
    <?php endwhile; ?>

    <hr>
    <h1>Videos</h1>
    <?php while ($row = $videos->fetch_assoc()): ?>
        <?php if (!empty($row['url'])): ?>
            <iframe width="400" height="300" src="<?= htmlspecialchars($row['url']) ?>" allowfullscreen></iframe>
        <?php endif; ?>
    <?php endwhile; ?>

    <hr>
    <h1>Ads</h1>
    <?php while ($ad = $ads->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($ad['title']) ?></h3>
        <p><?= htmlspecialchars($ad['description']) ?></p>
        <?php if (!empty($ad['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($ad['image']) ?>" width="200">
        <?php endif; ?>

        <h4>Icons:</h4>
        <?php
        $icons = $conn->query("SELECT * FROM ad_icons WHERE ad_id = " . intval($ad['id']));
        while ($icon = $icons->fetch_assoc()):
            $iconValue = htmlspecialchars($icon['icon']);
            ?>
            <p>
                <?php
                if (preg_match('/\.(png|jpe?g|gif|svg)$/i', $iconValue)) {
                    echo '<img src="uploads/' . $iconValue . '" width="30">';
                } else {
                    echo '<i class="' . $iconValue . '"></i>';
                }
                ?>
                - <?= htmlspecialchars($icon['title']) ?> : <?= htmlspecialchars($icon['text']) ?>
            </p>
        <?php endwhile; ?>
    <?php endwhile; ?>

    <hr>
    <h1>Why Choose Us</h1>
    <?php while ($row = $questions->fetch_assoc()): ?>
        <h3>Q: <?= htmlspecialchars($row['question']) ?></h3>
        <p>A: <?= htmlspecialchars($row['answer']) ?></p>
        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
        <?php endif; ?>
    <?php endwhile; ?>

    <hr>
    <h1>Our Services</h1>
    <?php while ($row = $services->fetch_assoc()): ?>
        <?php if (!empty($row['icon'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['icon']) ?>" width="50">
        <?php endif; ?>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
    <?php endwhile; ?>

</body>

</html>