<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = trim($_POST['code']);

    if (isset($_SESSION['verify_code']) && $input_code == $_SESSION['verify_code']) {
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['username'] = $_SESSION['temp_username'];

        unset($_SESSION['verify_code']);
        unset($_SESSION['verify_email']);
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_username']);

        header("Location: index.php");
        exit();
    } else {
        $error = "Incorrect code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Verify Code</title>
    <link rel="stylesheet" href="./assets/css/dashboard.css">

    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/config.js"></script>
</head>

<body class="d-flex align-items-center justify-content-center">
    <div class="w-100" style="max-width: 400px;">
        <h3 class="mb-3">Email Verification</h3>
        <p>Enter the 6-digit code sent to your email.</p>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="code" placeholder="Enter code" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Verify</button>
        </form>
    </div>
</body>

</html>