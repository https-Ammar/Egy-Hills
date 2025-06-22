<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = trim($_POST['price'] ?? '');

    $stmt = $conn->prepare("UPDATE projects SET title = ?, location = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $location, $price, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}
?>