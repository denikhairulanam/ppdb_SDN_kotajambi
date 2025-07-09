<?php
session_start();
require 'db.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // Redirect berdasarkan role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - SDN 004 Sei Lala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100" style="background: #f9fbfd;">
    <form method="POST" class="bg-white p-5 rounded shadow" style="width: 360px;">
        <div class="text-center mb-4">
            <img src="logo.png" alt="Logo" width="80">
            <h5 class="mt-3">SD Negeri 189 kota jambi</h5>
            <small>JL. Rajawali, No. 1 RT 19/06, Tambaksari, Jambi Selatan, Rw. Sari, Kec. Kota Baru, Kota Jambi, Jambi 36138</small>
        </div>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        <div class="mt-3 text-center">
            <a href="register.php">Belum punya akun?</a>
        </div>
    </form>
</body>

</html>