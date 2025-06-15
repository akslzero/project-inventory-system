<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($user->login($username, $password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;

        header("Location: index.php");
        exit;
    } else {
        $error = "Login gagal! Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 400px;">
    <h3 class="mb-4 text-center">Login Sistem Inventori</h3>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </div>
</form>

</div>
</body>
</html>
