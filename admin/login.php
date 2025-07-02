<?php
session_start();
include 'db.php';
require __DIR__ . '/vendor/autoload.php';
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $code = random_int(100000, 999999);
                $_SESSION['verify_code'] = $code;
                $_SESSION['verify_email'] = $email;
                $_SESSION['temp_user_id'] = $id;
                $_SESSION['temp_username'] = $username;
                $_SESSION['role'] = $role;

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ammar132004@gmail.com';
                    $mail->Password = 'okcejzwtuepbqgyq';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
                    $mail->addAddress('ammar132004@gmail.com');
                    $mail->Subject = 'Your verification code';
                    $mail->Body = "Verification code for user $email is: $code";
                    $mail->send();

                    header("Location: verify.php");
                    exit();
                } catch (Exception $e) {
                    $error = "Verification email could not be sent.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Email not found.";
        }
        $stmt->close();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <link href="assets/css/vendor.min.css" rel="stylesheet" />
    <link href="assets/css/icons.min.css" rel="stylesheet" />
    <link href="assets/css/app.min.css" rel="stylesheet" />
    <script src="assets/js/config.js"></script>
</head>

<body class="d-flex align-items-center justify-content-center">
    <div class="w-100" style="max-width: 400px;">
        <h3 class="mb-3">Egy-Hills</h3>
        <p class="mb-4">Welcome to Neo<br>Please sign in to your account.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
            <div class="mb-3">
                <input type="email" class="form-control" placeholder="Email address" name="email" required
                    autocomplete="username" />
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" placeholder="Password" name="password" required
                    autocomplete="current-password" />
            </div>
            <div class="form-check text-start mb-3">
                <input class="form-check-input" type="checkbox" id="checkMeOut">
                <label class="form-check-label" for="checkMeOut">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
        </form>

        <p class="mt-3">Not registered? <a href="register.php">Create an account</a></p>
    </div>
</body>

</html>