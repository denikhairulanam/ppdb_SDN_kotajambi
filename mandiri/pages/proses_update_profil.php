<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = $_POST['id'];
    $nik         = htmlspecialchars($_POST['nik']);
    $nama_ortu   = htmlspecialchars($_POST['nama_ortu']);
    $alamat      = htmlspecialchars($_POST['alamat']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);

    // Cek jika ada file foto diupload
    $foto_nama = null;
    if (!empty($_FILES['foto']['name'])) {
        $foto_nama = uniqid() . '_' . basename($_FILES['foto']['name']);
        $target_path = __DIR__ . '/../upload/' . $foto_nama;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
            echo "<script>alert('Gagal mengupload foto'); window.history.back();</script>";
            exit;
        }
    }

    // Simpan data utama siswa
    $stmt = $koneksi->prepare("UPDATE data_siswa SET nik=?, nama_ortu=?, alamat=?, asal_sekolah=? WHERE id=?");
    $stmt->bind_param("ssssi", $nik, $nama_ortu, $alamat, $asal_sekolah, $id);
    $stmt->execute();
    $stmt->close();

    // Simpan foto ke tabel upload jika ada
    if ($foto_nama) {
        // Cek apakah user sudah punya data di tabel upload
        $check = $koneksi->prepare("SELECT id FROM upload WHERE user_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Update
            $update = $koneksi->prepare("UPDATE upload SET foto=? WHERE user_id=?");
            $update->bind_param("si", $foto_nama, $id);
            $update->execute();
            $update->close();
        } else {
            // Insert
            $insert = $koneksi->prepare("INSERT INTO upload (user_id, foto) VALUES (?, ?)");
            $insert->bind_param("is", $id, $foto_nama);
            $insert->execute();
            $insert->close();
        }

        $check->close();
    }

    echo "<script>alert('Data berhasil diperbarui!'); window.location.href='dashboard_siswa.php';</script>";
} else {
    echo "<script>alert('Metode tidak diperbolehkan!'); window.location.href='dashboard_siswa.php';</script>";
}
