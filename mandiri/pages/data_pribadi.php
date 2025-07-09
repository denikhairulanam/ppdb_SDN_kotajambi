<?php
require_once __DIR__ . '/../db.php';

$user_id = $_SESSION['user']['id'];

// Fungsi upload file
function uploadFile($input_name)
{
    if (!empty($_FILES[$input_name]['name'])) {
        $target_dir = __DIR__ . '/../upload/';
        $filename = time() . '_' . basename($_FILES[$input_name]['name']);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
            return $filename;
        }
    }
    return null;
}

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nik = $_POST['nik'];
    $nama_ortu = $_POST['nama_ortu'];
    $alamat = $_POST['alamat'];

    // Upload file
    $kk         = uploadFile('kk');
    $akta       = uploadFile('akta');
    $pernyataan = uploadFile('pernyataan');
    $foto       = uploadFile('foto');

            // Update nama & email ke tabel user
            // Cek apakah email dipakai user lain
            $cek_email = $koneksi->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $cek_email->bind_param("si", $email, $user_id);
            $cek_email->execute();
            $email_result = $cek_email->get_result();

            if ($email_result->num_rows > 0) {
                echo "<div class='alert alert-danger'>Email sudah digunakan oleh pengguna lain.</div>";
            } else 
                // Lanjutkan update user
                $update_user = $koneksi->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
                $update_user->bind_param("ssi", $nama, $email, $user_id);
                $update_user->execute();

                // Refresh session
                $_SESSION['user']['nama'] = $nama;
                $_SESSION['user']['email'] = $email;

                // ... lanjut proses upload data pribadi seperti sebelumnya

                $update_user->execute();

    // Refresh session data
    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['email'] = $email;

    // Cek apakah data sudah ada
    $cek = $koneksi->prepare("SELECT * FROM upload WHERE user_id = ?");
    $cek->bind_param("i", $user_id);
    $cek->execute();
    $hasil = $cek->get_result();

    if ($hasil->num_rows > 0) {
        // UPDATE
        $sql = "UPDATE upload SET nik=?, nama_ortu=?, alamat=?";
        if ($kk)         $sql .= ", kk='$kk'";
        if ($akta)       $sql .= ", akta='$akta'";
        if ($pernyataan) $sql .= ", pernyataan='$pernyataan'";
        if ($foto)       $sql .= ", foto='$foto'";
        $sql .= " WHERE user_id=?";

        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssi", $nik, $nama_ortu, $alamat, $user_id);
        $stmt->execute();
    } else {
        // INSERT
        $stmt = $koneksi->prepare("INSERT INTO upload (user_id, nik, nama_ortu, alamat, kk, akta, pernyataan, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $nik, $nama_ortu, $alamat, $kk, $akta, $pernyataan, $foto);
        $stmt->execute();
    }

    echo "<div class='alert alert-success'>Data berhasil disimpan.</div>";
}

// Ambil data upload siswa
$ambil = $koneksi->prepare("SELECT * FROM upload WHERE user_id = ?");
$ambil->bind_param("i", $user_id);
$ambil->execute();
$result = $ambil->get_result();
$data = $result->fetch_assoc() ?: [];

?>

<h4>Form Data Pribadi</h4>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['nama'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>NIK</label>
        <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($data['nik'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Nama Orang Tua</label>
        <input type="text" name="nama_ortu" class="form-control" value="<?= htmlspecialchars($data['nama_ortu'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label>Upload Foto Diri</label>
        <input type="file" name="foto" class="form-control">
        <?php if (!empty($data['foto'])): ?>
            <p class="mt-2"><img src="../upload/<?= htmlspecialchars($data['foto']) ?>" width="100" alt="Foto Siswa"></p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Kartu Keluarga (KK)</label>
        <input type="file" name="kk" class="form-control">
        <?php if (!empty($data['kk'])): ?>
            <p class="mt-2"><a href="../upload/<?= htmlspecialchars($data['kk']) ?>" target="_blank">Lihat KK</a></p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Akta Kelahiran</label>
        <input type="file" name="akta" class="form-control">
        <?php if (!empty($data['akta'])): ?>
            <p class="mt-2"><a href="../upload/<?= htmlspecialchars($data['akta']) ?>" target="_blank">Lihat Akta</a></p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Surat Pernyataan Orang Tua</label>
        <input type="file" name="pernyataan" class="form-control">
        <?php if (!empty($data['pernyataan'])): ?>
            <p class="mt-2"><a href="../upload/<?= htmlspecialchars($data['pernyataan']) ?>" target="_blank">Lihat Pernyataan</a></p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Data</button>
</form>