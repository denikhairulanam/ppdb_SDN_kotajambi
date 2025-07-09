<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Tambah Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_admin'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';

    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . '_' . basename($_FILES["foto"]["name"]);
        $targetDir = __DIR__ . '/../upload_admin/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $targetDir . $foto);
    }

    $stmt = $koneksi->prepare("INSERT INTO users (nama, username, email, password, role, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $username, $email, $password, $role, $foto);
    $stmt->execute();
    header("Location: admin_dashboard.php?page=manage_admin");
    exit;
}

// Hapus Admin
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php?page=manage_admin");
    exit;
}

// Update Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . '_' . basename($_FILES["foto"]["name"]);
        $targetDir = __DIR__ . '/../upload_admin/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $targetDir . $foto);
    }

    if ($password && $foto) {
        $stmt = $koneksi->prepare("UPDATE users SET nama=?, username=?, email=?, password=?, foto=? WHERE id=?");
        $stmt->bind_param("sssssi", $nama, $username, $email, $password, $foto, $id);
    } elseif ($password) {
        $stmt = $koneksi->prepare("UPDATE users SET nama=?, username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $username, $email, $password, $id);
    } elseif ($foto) {
        $stmt = $koneksi->prepare("UPDATE users SET nama=?, username=?, email=?, foto=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $username, $email, $foto, $id);
    } else {
        $stmt = $koneksi->prepare("UPDATE users SET nama=?, username=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $username, $email, $id);
    }

    $stmt->execute();
    header("Location: admin_dashboard.php?page=manage_admin");
    exit;
}

$admins = $koneksi->query("SELECT * FROM users WHERE role = 'admin'");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h3 class="mb-4">Daftar Admin</h3>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Admin</button>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $admins->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../upload_admin/<?= htmlspecialchars($row['foto'] ?? 'default-user.png') ?>" width="50" height="50" style="object-fit:cover;border-radius:50%"></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                            <a href="admin_dashboard.php?page=manage_admin&hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php
    $admins->data_seek(0);
    while ($row = $admins->fetch_assoc()): ?>
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" class="modal-content">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
                        </div>
                        <div class="mb-2">
                            <label>Password (kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Foto (opsional)</label>
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
    <?php endwhile; ?>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah_admin" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>