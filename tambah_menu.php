<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_makanan']);
    $kategori = $_POST['kategori'];
    $harga = (int)$_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $status = $_POST['status'];

    if (empty($nama) || $harga <= 0) {
        $error = "Nama dan harga wajib diisi dengan benar!";
    } else {
        $foto = 'default.jpg';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $upload = uploadFoto($_FILES['foto']);
            if (is_string($upload) && strpos($upload, '.') !== false) {
                $foto = $upload;
            } else {
                $error = $upload;
            }
        }

        if (!$error) {
            $query = "INSERT INTO menu (nama_makanan, kategori, harga, deskripsi, foto, status) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssisss", $nama, $kategori, $harga, $deskripsi, $foto, $status);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Menu berhasil ditambah!";
            } else {
                $error = "Gagal menyimpan ke database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Admin</title>
    <style>
        body { background: linear-gradient(rgba(6, 30, 63, 0.85), rgba(6, 30, 63, 0.85)), url('Pictures/bglautmermaid.jpeg'); color: white; font-family: 'Inter', sans-serif; padding: 120px 40px 40px; }
        .form-container { max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 20px; backdrop-filter: blur(10px); }
        h2 { text-align: center; color: #FFD700; font-family: 'Marko One', serif; }
        label { display: block; margin: 15px 0 5px; color: #FFD700; }
        input, select, textarea { width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.2); border: none; color: white; }
        button { background: #B0C4DE; color: #000; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        .error { color: #FF6B6B; text-align: center; }
        .success { color: #98FB98; text-align: center; }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="form-container">
        <h2>Tambah Menu Baru</h2>

        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?> <a href="admin_dashboard.php">Kembali ke Dashboard</a></p><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Nama Makanan</label>
            <input type="text" name="nama_makanan" required>

            <label>Kategori</label>
            <select name="kategori" required>
                <option value="appetizer">Appetizer / Signature</option>
                <option value="main_course">Main Course</option>
                <option value="dessert">Dessert</option>
                <option value="drink">Drink</option>
            </select>

            <label>Harga (Rp)</label>
            <input type="number" name="harga" min="1" required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4"></textarea>

            <label>Foto Menu (JPG/PNG, max 2MB)</label>
            <input type="file" name="foto" accept="image/jpeg,image/png">

            <label>Status</label>
            <select name="status">
                <option value="tersedia">Tersedia</option>
                <option value="habis">Habis</option>
            </select>

            <button type="submit">Simpan Menu</button>
        </form>
    </div>

</body>
</html>