<?php
require_once __DIR__ . '/../db.php';

$user_id = $_SESSION['user']['id'];

$stmt = $koneksi->prepare("SELECT * FROM upload WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f6fc;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007bff;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center p-4">
                    <img src="<?= !empty($data['foto']) ? '../upload/' . htmlspecialchars($data['foto']) : '../assets/default-user.png' ?>" class="profile-img mx-auto mb-3" alt="Foto Profil">
                    <h5 class="fw-bold"><?= htmlspecialchars($_SESSION['user']['nama'] ?? '') ?></h5>
                    <p class="text-muted mb-1">Siswa Baru</p>
                    <p class="text-muted">SDN 189</p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-4">
                    <h5 class="mb-4 fw-bold">Informasi Siswa</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Nama Lengkap</div>
                        <div class="col-sm-8"><?= htmlspecialchars($_SESSION['user']['nama'] ?? '') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Email</div>
                        <div class="col-sm-8"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">NIK</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['nik'] ?? 'Belum diisi') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Nama Orang Tua</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['nama_ortu'] ?? 'Belum diisi') ?></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 fw-semibold">Alamat</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['alamat'] ?? 'Belum diisi') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>