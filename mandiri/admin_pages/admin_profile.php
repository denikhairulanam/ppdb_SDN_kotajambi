<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$admin_id = $_SESSION['user']['id'];
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Admin</title>
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
                    <img src="<?= !empty($data['foto']) ? '../upload_admin/' . htmlspecialchars($data['foto']) : '../assets/default-user.png' ?>"
                        class="profile-img mx-auto mb-3" alt="Foto Admin">
                    <h5 class="fw-bold"><?= htmlspecialchars($data['nama']) ?></h5>
                    <p class="text-muted mb-1">Administrator</p>
                    <p class="text-muted">PPDB SDN 189</p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-4">
                    <h5 class="mb-4 fw-bold">Informasi Admin</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Nama Lengkap</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['nama']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Username</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['username']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-semibold">Email</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['email']) ?: 'Belum diisi' ?></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 fw-semibold">Role</div>
                        <div class="col-sm-8"><?= htmlspecialchars($data['role']) ?></div>
                    </div>
                    <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalEditProfil">Edit Profil</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil Admin -->
    <div class="modal fade" id="modalEditProfil" tabindex="-1" aria-labelledby="modalEditProfilLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="/admin_pages/update_admin.php" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditProfilLabel">Edit Profil Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
                    </div>
                    <div class="mb-2">
                        <label>Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Foto Baru (opsional)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_admin" class="btn btn-success">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>