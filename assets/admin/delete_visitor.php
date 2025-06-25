<?php
include 'db.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM visitors WHERE id = $id");

header("Location: dashboard.php");
exit();
