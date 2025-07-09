

<?php
require_once __DIR__ . '/../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$admin_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Penanganan foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

        if (in_array($foto_ext, $allowed_ext)) {
            $new_foto_name = 'admin_' . time() . '.' . $foto_ext;
            $upload_dir = __DIR__ . '/../upload_admin/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $upload_path = $upload_dir . $new_foto_name;

            if (move_uploaded_file($foto_tmp, $upload_path)) {
                $foto = $new_foto_name;
            }
        } else {
            echo "<script>alert('Format file tidak didukung (hanya JPG, PNG).'); history.back();</script>";
            exit;
        }
    }

    // Siapkan query dinamis
    $query  = "UPDATE users SET nama=?, username=?, email=?";
    $params = [$nama, $username, $email];
    $types  = "sss";

    if ($password) {
        $query .= ", password=?";
        $params[] = $password;
        $types .= "s";
    }

    if ($foto) {
        $query .= ", foto=?";
        $params[] = $foto;
        $types .= "s";
    }

    $query .= " WHERE id=?";
    $params[] = $admin_id;
    $types .= "i";

    // Eksekusi prepared statement
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('Profil berhasil diperbarui'); location.href='/admin_dashboard.php?page=admin_profile';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil'); history.back();</script>";
    }
}
