<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}



// === FUNGSI UPLOAD FILE ===
function uploadFile($field)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!empty($_FILES[$field]['name']) && in_array($_FILES[$field]['type'], $allowedTypes)) {
        $targetDir = "../upload/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $filename = time() . '_' . basename($_FILES[$field]['name']);
        $filepath = $targetDir . $filename;
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $filepath)) {
            return $filename;
        }
    }
    return null;
}

// === TAMBAH SISWA ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $username   = $_POST['username'];
    $nama       = $_POST['nama'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nik        = $_POST['nik'];
    $nama_ortu  = $_POST['nama_ortu'];
    $alamat     = $_POST['alamat'];
    $foto       = uploadFile('foto');
    $kk         = uploadFile('kk');
    $akta       = uploadFile('akta');
    $pernyataan = uploadFile('pernyataan');
    $role       = 'siswa';

    // Validasi username
    $cekUsername = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
    $cekUsername->bind_param("s", $username);
    $cekUsername->execute();
    $cekUsername->store_result();
    if ($cekUsername->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
        exit;
    }
    $cekUsername->close();

    // Validasi email
    $cekEmail = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $cekEmail->bind_param("s", $email);
    $cekEmail->execute();
    $cekEmail->store_result();
    if ($cekEmail->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.history.back();</script>";
        exit;
    }
    $cekEmail->close();

    // Simpan ke tabel users
    $stmtUser = $koneksi->prepare("INSERT INTO users (username, nama, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmtUser->bind_param("sssss", $username, $nama, $email, $password, $role);
    $stmtUser->execute();
    $user_id = $koneksi->insert_id;

    // Simpan ke tabel upload
    $stmtUpload = $koneksi->prepare("INSERT INTO upload (user_id, nik, nama_ortu, alamat, foto, kk, akta, pernyataan, status_pendaftaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')");
    $stmtUpload->bind_param("isssssss", $user_id, $nik, $nama_ortu, $alamat, $foto, $kk, $akta, $pernyataan);
    $stmtUpload->execute();

    header("Location: admin_dashboard.php?page=manage_siswa");
    exit;
}

// === AMBIL DATA SISWA DARI TABEL UPLOAD (dengan informasi user) ===
$query = "
SELECT
  up.user_id,
  u.nama,
  u.email,
  up.nik,
  up.nama_ortu,
  up.alamat,
  up.foto,
  up.kk,
  up.akta,
  up.pernyataan,
  up.status_pendaftaran,
  COALESCE(up.pengumuman, '') AS pengumuman
FROM upload up
JOIN users u ON u.id = up.user_id
WHERE u.role = 'siswa'
ORDER BY up.user_id DESC
";
$siswa = $koneksi->query($query);
?>
<!-- === HTML MULAI === -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h3 class="mb-4">Daftar Siswa</h3>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Siswa</button>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIK</th>
                    <th>Nama Ortu</th>
                    <th>Alamat</th>
                    <th>Dokumen</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $siswa->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../upload/<?= htmlspecialchars($row['foto'] ?? 'default-user.png') ?>" width="50" height="50" style="object-fit:cover;border-radius:50%"></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['nik'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['nama_ortu'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($row['kk'])): ?>
                                <a href="../upload/<?= htmlspecialchars($row['kk']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">Lihat KK</a><br>
                            <?php endif; ?>
                            <?php if (!empty($row['akta'])): ?>
                                <a href="../upload/<?= htmlspecialchars($row['akta']) ?>" target="_blank" class="btn btn-sm btn-outline-success mb-1">Lihat Akta</a><br>
                            <?php endif; ?>
                            <?php if (!empty($row['pernyataan'])): ?>
                                <a href="../upload/<?= htmlspecialchars($row['pernyataan']) ?>" target="_blank" class="btn btn-sm btn-outline-warning mb-1">Lihat Pernyataan</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= ($row['status_pendaftaran'] === 'Diterima') ? 'success' : (($row['status_pendaftaran'] === 'Ditolak') ? 'danger' : 'secondary') ?>">
                                <?= $row['status_pendaftaran'] ?? 'Menunggu' ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success verifikasi-btn" data-id="<?= $row['user_id'] ?>" data-verifikasi="terima">Terima</button>
                            <button class="btn btn-sm btn-danger verifikasi-btn" data-id="<?= $row['user_id'] ?>" data-verifikasi="tolak">Tolak</button>
                        </td>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>

    <!-- === MODAL TAMBAH SISWA === -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $fields = [
                        "username" => "Username",
                        "nama" => "Nama",
                        "email" => "Email",
                        "password" => "Password",
                        "nik" => "NIK",
                        "nama_ortu" => "Nama Orang Tua",
                        "alamat" => "Alamat",
                        "foto" => "Foto",
                        "kk" => "Kartu Keluarga (KK)",
                        "akta" => "Akta Kelahiran",
                        "pernyataan" => "Surat Pernyataan"
                    ];
                    foreach ($fields as $name => $label): ?>
                        <div class="mb-2">
                            <label><?= $label ?></label>
                            <?php if ($name === 'alamat'): ?>
                                <textarea name="<?= $name ?>" class="form-control" required></textarea>
                            <?php elseif (in_array($name, ['foto', 'kk', 'akta', 'pernyataan'])): ?>
                                <input type="file" name="<?= $name ?>" class="form-control">
                            <?php else: ?>
                                <input type="<?= $name === 'email' ? 'email' : ($name === 'password' ? 'password' : 'text') ?>" name="<?= $name ?>" class="form-control" required>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah_siswa" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '.verifikasi-btn', function() {
            const userId = $(this).data('id');
            const verifikasi = $(this).data('verifikasi');
            const row = $(this).closest('tr');

            if (confirm(`Yakin ingin ${verifikasi} siswa ini?`)) {
                $.post('admin_pages/verifikasi_siswa.php', {
                    id: userId,
                    verifikasi: verifikasi
                }, function(res) {
                    if (res.success) {
                        const badgeClass = res.status === 'Diterima' ? 'success' : 'danger';
                        row.find('td:nth-child(8)').html(`<span class="badge bg-${badgeClass}">${res.status}</span>`);
                        alert("Status berhasil diubah menjadi: " + res.status);
                    } else {
                        alert('Gagal memverifikasi.');
                    }
                }, 'json');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>