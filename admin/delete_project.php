<?php
// delete_project.php

include 'db.php'; // تأكد أن هذا الملف فيه اتصال قاعدة البيانات في المتغير $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = intval($_POST['id']);

        // استخدم Prepared Statement للحماية
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo 'success'; // مهم أن ترجع كلمة success فقط
        } else {
            echo 'error';
        }

        $stmt->close();
    } else {
        echo 'invalid_id';
    }
} else {
    echo 'invalid_request';
}
?>