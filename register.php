<?php
include 'db.php';
session_start();

$success = "";
$error = "";

if (isset($_POST['register'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // تأكد أن الإيميل أو اسم المستخدم غير مسجل من قبل
    $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Username or Email already exists.";
    } else {
        if ($conn->query("INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')")) {
            $success = "Account created successfully. <a href='login.php'>Login here</a>";
        } else {
            $error = "Error creating account. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Neo - Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #0d0d1f;
            color: #ccc;
            min-height: 100vh;
        }

        .form-control {
            background-color: #1a1a2e;
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: #1a1a2e;
            color: #fff;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #b19cd9;
            border: none;
        }

        .btn-primary:hover {
            background-color: #9a7dcd;
        }

        a {
            color: #90ee90;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">
    <div class="text-center w-100" style="max-width: 400px;">
        <h3 class="mb-3 text-success">Neo</h3>
        <p class="mb-4">Create a new account</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <input name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input name="email" type="email" class="form-control" placeholder="Email address" required>
            </div>
            <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <button name="register" type="submit" class="btn btn-primary w-100 mb-3">Register</button>
        </form>

        <p class="mt-3">Already have an account? <a href="login.php">Sign In</a></p>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>