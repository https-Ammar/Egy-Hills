<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;  // خزن اسم المستخدم في السيشن
            header("Location:index.php");
            exit();
        } else {
            $error = "Wrong password.";
        }
    } else {
        $error = "Email not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Neo - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./assets/css/dashboard.css">

</head>

<body class="d-flex align-items-center justify-content-center">
    <div class=" w-100" style="max-width: 400px;">
        <h3 class="mb-3 text-success">Neo</h3>
        <p class="mb-4">Welcome to Neo<br>Please Sign-in to your account.</p>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" class="form-control" placeholder="Email address" name="email" required />
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" placeholder="Password" name="password" required />
            </div>
            <div class="form-check text-start mb-3">
                <input class="form-check-input" type="checkbox" id="checkMeOut">
                <label class="form-check-label" for="checkMeOut">
                    Check me out
                </label>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
        </form>
        <p class="mt-3">Not registered? <a href="register.php">Create an account</a></p>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>