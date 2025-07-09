<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['verifikasi'])) {
    $user_id = (int) $_POST['id'];
    $verifikasi = $_POST['verifikasi'];

    if (!in_array($verifikasi, ['terima', 'tolak'])) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    $status = $verifikasi === 'terima' ? 'Diterima' : 'Ditolak';
    $pengumuman = $status === 'Diterima'
        ? 'Selamat! Anda dinyatakan diterima. Silakan menunggu informasi selanjutnya dari panitia.'
        : 'Mohon maaf, Anda tidak diterima. Terima kasih telah mendaftar.';

    $stmt = $koneksi->prepare("UPDATE upload SET status_pendaftaran = ?, pengumuman = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $status, $pengumuman, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'status' => $status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update database']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
