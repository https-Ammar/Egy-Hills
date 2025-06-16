<?php
include 'db.php';

// ✅ جلب البيانات من قاعدة البيانات
$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$ad_icons = $conn->query("SELECT * FROM ad_icons");
$projects = $conn->query("SELECT * FROM projects");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");

// ✅ حفظ بيانات الزائر (الاسم ورقم الهاتف)
if (isset($_POST['submit_visitor'])) {
    $name = trim($_POST['visitor_name']);
    $phone = trim($_POST['visitor_phone']);

    if (!empty($name) && !empty($phone)) {
        $stmt = $conn->prepare("INSERT INTO visitors (name, phone) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $phone);
        $stmt->execute();
        echo "<p style='color:green;'>Your inquiry has been received. Thank you!</p>";
    } else {
        echo "<p style='color:red;'>Please fill in both fields.</p>";
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

    <hr>
    <h2>Booking / Inquiry</h2>


    <hr>
    <hr>
    <hr>

    <h1>Main Slider</h1>
    <?php while ($row = $sliders->fetch_assoc()): ?>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
    <?php endwhile; ?>

    <hr>

    <h1>About Us</h1>
    <p>This is the about section content from sliders and cards only.</p>
    <h2>About Sliders</h2>
    <?php while ($row = $about_sliders->fetch_assoc()): ?>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
    <?php endwhile; ?>

    <h2>About Cards</h2>
    <?php while ($row = $about_cards->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
    <?php endwhile; ?>

    <hr>

    <h1>Property Highlights</h1>
    <?php while ($row = $highlights->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
    <?php endwhile; ?>

    <hr>

    <h1>Videos</h1>
    <?php while ($row = $videos->fetch_assoc()): ?>
        <iframe width="400" height="300" src="<?= htmlspecialchars($row['url']) ?>"></iframe>
    <?php endwhile; ?>

    <hr>

    <h1>Ads</h1>
    <?php while ($ad = $ads->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($ad['title']) ?></h3>
        <p><?= htmlspecialchars($ad['description']) ?></p>
        <img src="uploads/<?= htmlspecialchars($ad['image']) ?>" width="200">

        <h4>Icons:</h4>
        <?php
        $icons = $conn->query("SELECT * FROM ad_icons WHERE ad_id=" . intval($ad['id']));
        while ($icon = $icons->fetch_assoc()):
            ?>
            <p>
                <?php
                $iconValue = htmlspecialchars($icon['icon']);
                if (preg_match('/\.(png|jpg|jpeg|svg|gif)$/i', $iconValue)) {
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
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="200">
    <?php endwhile; ?>

    <hr>

</body>

</html>