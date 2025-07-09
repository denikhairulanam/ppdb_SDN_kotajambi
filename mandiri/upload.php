<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = $_POST['id'];
    $nik         = htmlspecialchars($_POST['nik']);
    $nama_ortu   = htmlspecialchars($_POST['nama_ortu']);
    $alamat      = htmlspecialchars($_POST['alamat']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);

    $stmt = $koneksi->prepare("UPDATE data_siswa SET nik=?, nama_ortu=?, alamat=?, asal_sekolah=? WHERE id=?");
    $stmt->bind_param("ssssi", $nik, $nama_ortu, $alamat, $asal_sekolah, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='dashboard_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); window.history.back();</script>";
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "<script>alert('Metode tidak diperbolehkan!'); window.location.href='dashboard_siswa.php';</script>";
}
