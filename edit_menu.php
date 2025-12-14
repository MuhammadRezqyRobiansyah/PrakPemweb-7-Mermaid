<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = (int)$_GET['id'] ?? 0;
if ($id == 0) {
    header("Location: admin_dashboard.php");
    exit();
}

// Ambil data menu lama
$query = "SELECT * FROM menu WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$menu = mysqli_fetch_assoc($result);

if (!$menu) {
    header("Location: admin_dashboard.php");
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
        $foto = $menu['foto']; // default pakai foto lama

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $upload = uploadFoto($_FILES['foto']);
            if (is_string($upload) && strpos($upload, '.') !== false) {
                // Hapus foto lama kalau bukan default
                if ($menu['foto'] != 'default.jpg' && file_exists("uploads/" . $menu['foto'])) {
                    unlink("uploads/" . $menu['foto']);
                }
                $foto = $upload;
            } else {
                $error = $upload;
            }
        }

        if (!$error) {
            $query = "UPDATE menu SET nama_makanan = ?, kategori = ?, harga = ?, deskripsi = ?, foto = ?, status = ? WHERE id = ?";
            $stmt2 = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt2, "ssisssi", $nama, $kategori, $harga, $deskripsi, $foto, $status, $id);
            if (mysqli_stmt_execute($stmt2)) {
                $success = "Menu berhasil diupdate!";
                // Refresh data menu untuk tampilan
                $menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM menu WHERE id = $id"));
            } else {
                $error = "Gagal update database.";
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
    <title>Edit Menu - Admin</title>
    <style>
        body { background: linear-gradient(rgba(6, 30, 63, 0.85), rgba(6, 30, 63, 0.85)), url('Pictures/bglautmermaid.jpeg'); color: white; font-family: 'Inter', sans-serif; padding: 120px 40px 40px; margin: 0; }
        .form-container { max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 20px; backdrop-filter: blur(10px); }
        h2 { text-align: center; color: #FFD700; font-family: 'Marko One', serif; font-size: 32px; }
        label { display: block; margin: 15px 0 5px; color: #FFD700; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.2); border: none; color: white; font-size: 16px; }
        input[type="file"] { background: none; }
        button { background: #B0C4DE; color: #000; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px; font-size: 16px; }
        button:hover { background: #FFF; }
        .error { color: #FF6B6B; text-align: center; margin: 20px 0; }
        .success { color: #98FB98; text-align: center; margin: 20px 0; font-weight: bold; }
        .current-photo { text-align: center; margin: 20px 0; }
        .current-photo img { max-width: 300px; border-radius: 15px; }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="form-container">
        <h2>Edit Menu</h2>

        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?> <a href="admin_dashboard.php" style="color:#FFD700;">Kembali ke Dashboard</a></p><?php endif; ?>

        <div class="current-photo">
            <p>Foto Saat Ini:</p>
            <img src="uploads/<?= htmlspecialchars($menu['foto']) ?>" alt="Current">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label>Nama Makanan</label>
            <input type="text" name="nama_makanan" value="<?= htmlspecialchars($menu['nama_makanan']) ?>" required>

            <label>Kategori</label>
            <select name="kategori" required>
                <option value="appetizer" <?= $menu['kategori'] === 'appetizer' ? 'selected' : '' ?>>Appetizer / Signature</option>
                <option value="main_course" <?= $menu['kategori'] === 'main_course' ? 'selected' : '' ?>>Main Course</option>
                <option value="dessert" <?= $menu['kategori'] === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                <option value="drink" <?= $menu['kategori'] === 'drink' ? 'selected' : '' ?>>Drink</option>
            </select>

            <label>Harga (Rp)</label>
            <input type="number" name="harga" value="<?= $menu['harga'] ?>" min="1" required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4"><?= htmlspecialchars($menu['deskripsi']) ?></textarea>

            <label>Ganti Foto (JPG/PNG, max 2MB) - Kosongkan kalau tidak ingin ganti</label>
            <input type="file" name="foto" accept="image/jpeg,image/png">

            <label>Status</label>
            <select name="status">
                <option value="tersedia" <?= $menu['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="habis" <?= $menu['status'] === 'habis' ? 'selected' : '' ?>>Habis</option>
            </select>

            <button type="submit">Update Menu</button>
        </form>
    </div>
</body>
</html>