<?php
require 'db.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $role     = 'siswa'; // default role

    // Cek apakah username sudah digunakan
    $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = $koneksi->prepare("INSERT INTO users (username, password, nama, email, role) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param("sssss", $username, $password, $nama, $email, $role);
        if ($query->execute()) {
            header("Location: index.php"); // ke halaman login
            exit;
        } else {
            $error = "Registrasi gagal: " . $query->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - SDN 004 Sei Lala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100" style="background: #f9fbfd;">
    <form method="POST" class="bg-white p-5 rounded shadow" style="width: 360px;">
        <h4 class="mb-4 text-center">Registrasi Akun</h4>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required placeholder="Masukkan email aktif">
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>

        <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>
        <div class="mt-3 text-center">
            <a href="index.php">Sudah punya akun?</a>
        </div>
    </form>
</body>

</html>