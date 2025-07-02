<?php
include 'db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Invalid email format");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: register.php?error=Password must be at least 6 characters");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: register.php?error=Username or Email already exists");
        exit();
    }

    $stmt->close();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $hashedPassword);

    if ($insert->execute()) {
        $insert->close();
        header("Location: login.php");
        exit();
    } else {
        $insert->close();
        header("Location: register.php?error=Error creating account");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/vendor.min.css" />
    <link rel="stylesheet" href="assets/css/icons.min.css" />
    <link rel="stylesheet" href="assets/css/app.min.css" />
    <script src="assets/js/config.js"></script>
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="w-100" style="max-width: 400px;">
        <h3 class="mb-3">Egy-Hills</h3>
        <p class="mb-4">Welcome to Neo<br>Please Sign-in to your account.</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required
                    autocomplete="username">
            </div>
            <div class="mb-3">
                <input name="email" type="email" class="form-control" placeholder="Email address" required
                    autocomplete="email">
            </div>
            <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Password" required
                    autocomplete="new-password">
            </div>
            <button name="register" type="submit" class="btn btn-primary w-100 mb-3">Register</button>
        </form>

        <p class="mt-3">Already have an account? <a href="login.php">Sign In</a></p>
    </div>
</body>

</html>