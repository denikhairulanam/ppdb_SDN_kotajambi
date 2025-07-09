<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    require_once(__DIR__ . '/../db.php');

    // Ambil jadwal daftar ulang dari database
    $query = $koneksi->query("SELECT * FROM pengaturan LIMIT 1");
$jadwal = $query->fetch_assoc();

$tanggal = $jadwal['tanggal_daftar_ulang'] ?? null;
$pengumuman = htmlspecialchars($jadwal['pengumuman'] ?? 'Belum ada pengumuman');
?>

<h3>Daftar Ulang</h3>
<p><strong>Batas waktu daftar ulang:</strong>
    <?= $tanggal ? date("d F Y", strtotime($tanggal)) : 'Belum ditentukan' ?>
</p>

<p><?= $pengumuman ?></p>