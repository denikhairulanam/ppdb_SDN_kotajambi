<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $koneksi->prepare("SELECT status_pendaftaran, pengumuman FROM upload WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$status = $data['status_pendaftaran'] ?? 'Menunggu';
$pengumuman = $data['pengumuman'] ?? '';

$pesan = "";
if ($status === 'Diterima') {
    $pesan = "<div class='alert alert-success'>Selamat! Anda telah <strong>DITERIMA</strong> di sekolah kami.</div>";
} elseif ($status === 'Ditolak') {
    $pesan = "<div class='alert alert-danger'>Maaf, Anda <strong>DITOLAK</strong>. Silakan hubungi admin untuk informasi lebih lanjut.</div>";
} else {
    $pesan = "<div class='alert alert-secondary'>Status Anda masih <strong>MENUNGGU</strong> verifikasi dari admin.</div>";
}
?>

<div class="container py-4">
    <h3>Pengumuman Hasil Pendaftaran</h3>
    <?= $pesan ?>

    <?php if (!empty($pengumuman)): ?>
        <div class="mt-3 alert alert-info">
            <strong>Pengumuman:</strong><br>
            <?= nl2br(htmlspecialchars($pengumuman)) ?>
        </div>
    <?php endif; ?>
</div>