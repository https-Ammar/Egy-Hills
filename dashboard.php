<?php
include 'db.php';

// === دالة رفع الملف (تدعم الصور و SVG) ===
function uploadFile($file)
{
    if (!empty($file['name'])) {
        $name = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' . $name);
        return $name;
    }
    return '';
}

// === Main Slider ===
if (isset($_POST['add_slider'])) {
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO sliders (image) VALUES ('$image')");
}
if (isset($_GET['delete_slider'])) {
    $conn->query("DELETE FROM sliders WHERE id=" . intval($_GET['delete_slider']));
}

// === About Slider ===
if (isset($_POST['add_about_slider'])) {
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO about_slider (image) VALUES ('$image')");
}
if (isset($_GET['delete_about_slider'])) {
    $conn->query("DELETE FROM about_slider WHERE id=" . intval($_GET['delete_about_slider']));
}

// === About Cards ===
if (isset($_POST['add_about_card'])) {
    $image = uploadFile($_FILES['image']);
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $link = $_POST['link'];
    $conn->query("INSERT INTO about_cards (image, title, description, link) VALUES ('$image','$title','$desc','$link')");
}
if (isset($_GET['delete_about_card'])) {
    $conn->query("DELETE FROM about_cards WHERE id=" . intval($_GET['delete_about_card']));
}

// === Property Highlights ===
if (isset($_POST['add_highlight'])) {
    $image = uploadFile($_FILES['image']);
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $conn->query("INSERT INTO highlights (image, title, description) VALUES ('$image','$title','$desc')");
}
if (isset($_GET['delete_highlight'])) {
    $conn->query("DELETE FROM highlights WHERE id=" . intval($_GET['delete_highlight']));
}

// === Videos ===
if (isset($_POST['add_video'])) {
    $url = $_POST['url'];
    $conn->query("INSERT INTO videos (url) VALUES ('$url')");
}
if (isset($_GET['delete_video'])) {
    $conn->query("DELETE FROM videos WHERE id=" . intval($_GET['delete_video']));
}

// === Ads ===
if (isset($_POST['add_ad'])) {
    $image = uploadFile($_FILES['image']);
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $conn->query("INSERT INTO ads (image, title, description) VALUES ('$image','$title','$desc')");
}
if (isset($_GET['delete_ad'])) {
    $conn->query("DELETE FROM ads WHERE id=" . intval($_GET['delete_ad']));
}

// === Ad Icons ===
if (isset($_POST['add_ad_icon'])) {
    $ad_id = $_POST['ad_id'];
    $icon = uploadFile($_FILES['icon']);
    $title = $_POST['title'];
    $text = $_POST['text'];
    $conn->query("INSERT INTO ad_icons (ad_id, icon, title, text) VALUES ($ad_id, '$icon','$title','$text')");
}
if (isset($_GET['delete_ad_icon'])) {
    $conn->query("DELETE FROM ad_icons WHERE id=" . intval($_GET['delete_ad_icon']));
}

// === Questions ===
if (isset($_POST['add_question'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $image = uploadFile($_FILES['image']);
    $conn->query("INSERT INTO questions (question, answer, image) VALUES ('$question','$answer','$image')");
}
if (isset($_GET['delete_question'])) {
    $conn->query("DELETE FROM questions WHERE id=" . intval($_GET['delete_question']));
}

// === Services ===
if (isset($_POST['add_service'])) {
    $icon = uploadFile($_FILES['icon']);
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $conn->query("INSERT INTO services (icon, title, description) VALUES ('$icon','$title','$desc')");
}
if (isset($_GET['delete_service'])) {
    $conn->query("DELETE FROM services WHERE id=" . intval($_GET['delete_service']));
}

// === جلب البيانات ===
$sliders = $conn->query("SELECT * FROM sliders");
$about_sliders = $conn->query("SELECT * FROM about_slider");
$about_cards = $conn->query("SELECT * FROM about_cards");
$highlights = $conn->query("SELECT * FROM highlights");
$videos = $conn->query("SELECT * FROM videos");
$ads = $conn->query("SELECT * FROM ads");
$ad_icons = $conn->query("SELECT * FROM ad_icons");
$questions = $conn->query("SELECT * FROM questions");
$services = $conn->query("SELECT * FROM services");
?>

<h1>Dashboard</h1>

<!-- === Main Slider === -->
<h2>Main Slider</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button name="add_slider">Add Slider</button>
</form>
<?php while ($row = $sliders->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100">
    <a href="?delete_slider=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === About Slider === -->
<h2>About Slider</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button name="add_about_slider">Add About Slider</button>
</form>
<?php while ($row = $about_sliders->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100">
    <a href="?delete_about_slider=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === About Cards === -->
<h2>About Cards</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="text" name="link" placeholder="Link" required>
    <button name="add_about_card">Add Card</button>
</form>
<?php while ($row = $about_cards->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100">
    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
    <a href="?delete_about_card=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === Highlights === -->
<h2>Property Highlights</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <button name="add_highlight">Add Highlight</button>
</form>
<?php while ($row = $highlights->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100">
    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
    <a href="?delete_highlight=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === Videos === -->
<h2>Videos</h2>
<form method="POST">
    <input type="url" name="url" placeholder="Video URL" required>
    <button name="add_video">Add Video</button>
</form>
<?php while ($row = $videos->fetch_assoc()): ?>
    <?= htmlspecialchars($row['url']) ?>
    <a href="?delete_video=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === Ads === -->
<h2>Ads</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <button name="add_ad">Add Ad</button>
</form>
<?php while ($row = $ads->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100">
    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
    <a href="?delete_ad=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<h3>Add Icon to Ad</h3>
<form method="POST" enctype="multipart/form-data">
    <select name="ad_id" required>
        <?php
        $ads2 = $conn->query("SELECT * FROM ads");
        while ($a = $ads2->fetch_assoc()) {
            echo "<option value='{$a['id']}'>" . htmlspecialchars($a['title']) . "</option>";
        }
        ?>
    </select>
    <input type="file" name="icon" accept="image/*,.svg" required>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="text" placeholder="Text" required></textarea>
    <button name="add_ad_icon">Add Icon</button>
</form>

<h3>All Ad Icons</h3>
<?php while ($icon = $ad_icons->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($icon['icon']) ?>" width="50">
    <strong><?= htmlspecialchars($icon['title']) ?></strong><br>
    <?= htmlspecialchars($icon['text']) ?><br>
    <a href="?delete_ad_icon=<?= intval($icon['id']) ?>">Delete Icon</a><br>
<?php endwhile; ?>

<hr>

<!-- === Questions === -->
<h2>Why Choose Us</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="question" placeholder="Question" required>
    <textarea name="answer" placeholder="Answer" required></textarea>
    <input type="file" name="image">
    <button name="add_question">Add Q&A</button>
</form>
<?php while ($row = $questions->fetch_assoc()): ?>
    <strong><?= htmlspecialchars($row['question']) ?></strong><br>
    <a href="?delete_question=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>

<hr>

<!-- === Services === -->
<h2>Our Real Estate Services</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="icon" accept="image/*,.svg" required>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <button name="add_service">Add Service</button>
</form>
<?php while ($row = $services->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($row['icon']) ?>" width="50">
    <strong><?= htmlspecialchars($row['title']) ?></strong><br>
    <?= htmlspecialchars($row['description']) ?><br>
    <a href="?delete_service=<?= intval($row['id']) ?>">Delete</a><br>
<?php endwhile; ?>