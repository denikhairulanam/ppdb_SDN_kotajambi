<?php
session_start();
require 'db.php';

// Verifikasi status siswa (Terima / Tolak)
if (isset($_GET['verifikasi'], $_GET['id'])) {
    $verifikasi = $_GET['verifikasi'] === 'terima' ? 'Diterima' : 'Ditolak';
    $user_id = (int) $_GET['id'];

    $update = $koneksi->prepare("UPDATE upload SET status = ? WHERE user_id = ?");
    $update->bind_param("si", $verifikasi, $user_id);
    $update->execute();

    // Redirect untuk mencegah refresh ulang submit
    header("Location: admin_dashboard.php?page=manage_users");
    exit;
}

// Cek login & role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ambil data admin dari database
$admin_id = $_SESSION['user']['id'];
$query = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$_SESSION['user'] = $result->fetch_assoc();
$admin = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - PPDB SDN 004</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
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
            background-color: #495057;
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
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h5 class="text-white mb-4">🛠️ Admin Panel </h5>
        <a href="admin_dashboard.php" class="<?= !isset($_GET['page']) ? 'active' : '' ?>">
            <i class="bi bi-speedometer"></i> Dashboard
        </a>
        <a href="admin_dashboard.php?page=admin_profile" class="<?= ($_GET['page'] ?? '') === 'admin_profile' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Profil Admin
        </a>
        <a href="admin_dashboard.php?page=manage_admin" class="<?= ($_GET['page'] ?? '') === 'manage_admin' ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i> Kelola Admin
        </a>
        <a href="admin_dashboard.php?page=manage_users" class="<?= ($_GET['page'] ?? '') === 'manage_users' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Data Siswa
        </a>
        <a href="admin_dashboard.php?page=jadwal_daftar_ulang" class="<?= ($_GET['page'] ?? '') === 'jadwal_daftar_ulang' ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i> Jadwal Daftar Ulang
        </a>
        <hr class="bg-light">
        <div class="mb-2">👋 Hai, <strong><?= htmlspecialchars($admin['nama'] ?? '') ?></strong></div>
        <a href="logout.php" class="btn btn-danger btn-sm w-100 mt-2">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $allowed_pages = ['admin_profile', 'manage_admin', 'manage_users', 'jadwal_daftar_ulang'];
            if (in_array($page, $allowed_pages)) {
                include "admin_pages/{$page}.php";
            } else {
                echo "<div class='alert alert-danger'>Halaman tidak ditemukan.</div>";
            }
        } else {
            echo '
                <div class="card shadow-sm border-info bg-light">
                    <div class="card-body">
                        <h3 class="card-title mb-3">Selamat Datang, <strong>' . htmlspecialchars($admin['nama'] ?? '') . '</strong>!</h3>
                        <p class="card-text">Gunakan menu di samping untuk mengelola data PPDB siswa dan admin.</p>
                    </div>
                </div>
            ';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>