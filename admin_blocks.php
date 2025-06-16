<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';

    // رفع الصورة
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($image_tmp, 'uploads/' . $image_name);
    }

    if ($title && $text && $image_name) {
        $stmt = $conn->prepare("INSERT INTO booking_info_blocks (title, text, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $text, $image_name);
        $stmt->execute();
        $stmt->close();
        echo "تم إضافة البلوك بنجاح.";
    } else {
        echo "يرجى تعبئة كل الحقول ورفع صورة.";
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <label>العنوان:</label><br>
    <input type="text" name="title" required><br><br>

    <label>النص:</label><br>
    <textarea name="text" rows="5" required></textarea><br><br>

    <label>الصورة:</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">أضف البلوك</button>
</form>