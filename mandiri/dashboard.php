<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$query = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$_SESSION['user'] = $result->fetch_assoc();
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa - PPDB SDN 189 </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #0d6efd;
            color: white;
            position: fixed;
        }

        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar a:hover,
        .sidebar .active {
            background-color: #0b5ed7;
        }

        .content {
            margin-left: 220px;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar p-3">
        <h5 class="text-white mb-4">📚 PPDB SDN 189 </h5>
        <a href="dashboard.php" class="<?= !isset($_GET['page']) ? 'active' : '' ?>"><i class="bi bi-house-door"></i>Dashboard</a>
        <a href="dashboard.php?page=profil" class="<?= ($_GET['page'] ?? '') === 'profil' ? 'active' : '' ?>"><i class="bi bi-person-circle"></i>Profil</a>
        <a href="dashboard.php?page=data_pribadi" class="<?= ($_GET['page'] ?? '') === 'data_pribadi' ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i>Data Pribadi</a>
        <a href="dashboard.php?page=pengumuman" class="<?= ($_GET['page'] ?? '') === 'pengumuman' ? 'active' : '' ?>"><i class="bi bi-megaphone"></i>Pengumuman</a>
        <a href="dashboard.php?page=daftar_ulang" class="<?= ($_GET['page'] ?? '') === 'daftar_ulang' ? 'active' : '' ?>"><i class="bi bi-arrow-repeat"></i>Daftar Ulang</a>
        <hr class="bg-light">
        <div class="mb-2">👋 Hai, <strong><?= htmlspecialchars($user['nama'] ?? '') ?></strong></div>
        <a href="logout.php" class="btn btn-danger btn-sm w-100 mt-2">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="content">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $allowed_pages = ['profil', 'data_pribadi', 'pengumuman', 'daftar_ulang'];
            if (in_array($page, $allowed_pages)) {
                include "pages/{$page}.php";
            } else {
                echo "<div class='alert alert-danger'>Halaman tidak ditemukan.</div>";
            }
        } else {
            echo "
                <div class='card shadow-sm border-success bg-success-subtle'>
                    <div class='card-body'>
                        <h3 class='card-title mb-3'>Selamat Datang, <strong>" . htmlspecialchars($user['nama'] ?? '') . "</strong>!</h3>
                        <p class='card-text'>Silakan pilih menu di samping untuk melengkapi data pendaftaran Anda.</p>
                    </div>
                </div>
            ";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>