<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Tambah atau Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $pengumuman = $_POST['pengumuman'];

    if (isset($_POST['id']) && $_POST['id'] !== '') {
        // Update
        $id = $_POST['id'];
        $stmt = $koneksi->prepare("UPDATE pengaturan SET tanggal_daftar_ulang = ?, pengumuman = ? WHERE id = ?");
        $stmt->bind_param("ssi", $tanggal, $pengumuman, $id);
    } else {
        // Tambah baru
        $stmt = $koneksi->prepare("INSERT INTO pengaturan (tanggal_daftar_ulang, pengumuman) VALUES (?, ?)");
        $stmt->bind_param("ss", $tanggal, $pengumuman);
    }

    $stmt->execute();
    header("Location: admin_dashboard.php?page=jadwal_daftar_ulang");
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM pengaturan WHERE id = $id");
    header("Location: admin_dashboard.php?page=jadwal_daftar_ulang");
    exit;
}

// Ambil semua data
$pengaturans = $koneksi->query("SELECT * FROM pengaturan ORDER BY id DESC");

// Ambil data untuk edit (jika ada)
$editData = null;
if (isset($_GET['edit'])) {
    $idEdit = $_GET['edit'];
    $result = $koneksi->query("SELECT * FROM pengaturan WHERE id = $idEdit");
    $editData = $result->fetch_assoc();
}
?>

<div class="container py-3">
    <h3 class="mb-4"><?= $editData ? 'Edit' : 'Tambah' ?> Jadwal Daftar Ulang</h3>

    <form method="POST" class="mb-5">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="tanggal">Tanggal Daftar Ulang</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control"
                value="<?= htmlspecialchars($editData['tanggal_daftar_ulang'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="pengumuman">Isi Pengumuman</label>
            <textarea name="pengumuman" id="pengumuman" rows="4" class="form-control"
                required><?= htmlspecialchars($editData['pengumuman'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editData ? 'Update' : 'Simpan' ?></button>
        <?php if ($editData): ?>
            <a href="admin_dashboard.php?page=jadwal_daftar_ulang" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </form>

    <h4 class="mb-3">Daftar Jadwal & Pengumuman</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Pengumuman</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = $pengaturans->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['tanggal_daftar_ulang']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['pengumuman'])) ?></td>
                    <td>
                        <a href="admin_dashboard.php?page=jadwal_daftar_ulang&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="admin_dashboard.php?page=jadwal_daftar_ulang&hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>