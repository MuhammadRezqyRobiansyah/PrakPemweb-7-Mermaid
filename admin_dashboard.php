<?php
session_start();
require_once 'koneksi.php';
require_once 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle hapus menu
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $foto_lama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM menu WHERE id = $id"))['foto'] ?? '';
    if ($foto_lama && $foto_lama != 'default.jpg' && file_exists("Pictures/$foto_lama")) {
        unlink("Pictures/$foto_lama");
    }
    mysqli_query($koneksi, "DELETE FROM menu WHERE id = $id");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle ubah status
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status_baru = $_GET['status'] === 'tersedia' ? 'tersedia' : 'habis';
    mysqli_query($koneksi, "UPDATE menu SET status = '$status_baru' WHERE id = $id");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle confirm reservasi
if (isset($_GET['confirm'])) {
    $res_id = (int)$_GET['confirm'];
    // Update status jadi confirmed (hanya kalau masih pending)
    mysqli_query($koneksi, "UPDATE reservations SET status = 'confirmed' WHERE id = $res_id AND status = 'pending'");
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thalassa Deep - Admin Dashboard</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(6, 30, 63, 0.85), rgba(6, 30, 63, 0.85)), url('bglautmermaid.jpeg');
            background-size: cover;
            background-attachment: fixed;
            color: #FFFFFF;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 40px 40px;
        }
        h1 {
            font-family: 'Marko One', serif;
            font-size: 36px;
            text-align: center;
            margin-bottom: 40px;
            color: #FFD700;
        }
        .add-btn {
            background-color: #B0C4DE;
            color: #000;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 30px;
            text-decoration: none;
            font-size: 16px;
        }
        .add-btn:hover {
            background-color: #FFF;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .menu-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .menu-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
        }
        .menu-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .menu-price {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #FFD700;
        }
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 15px;
        }
        .tersedia { background-color: #98FB98; color: #006400; }
        .habis { background-color: #FF6B6B; color: white; }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .actions a {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }
        .edit { background-color: #5D8AA8; color: white; }
        .hapus { background-color: #D32F2F; color: white; }
        .ubah-status { background-color: #FFA500; color: white; }
        .actions a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="container">
        <h1>Admin Dashboard - Menu Management</h1>

        <a href="tambah_menu.php" class="add-btn">+ Tambah Menu Baru</a>

        <div class="menu-grid">
            <?php
            $result = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY kategori, id");
            if (mysqli_num_rows($result) == 0) {
                echo "<p style='text-align:center; grid-column: 1/-1;'>Belum ada menu.</p>";
            }
            while ($row = mysqli_fetch_assoc($result)):
            ?>
                <div class="menu-card">
                    <img src="Pictures/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_makanan']) ?>">
                    <div class="menu-name"><?= htmlspecialchars($row['nama_makanan']) ?></div>
                    <div class="menu-price">Rp. <?= number_format($row['harga']) ?></div>
                    <span class="status <?= $row['status'] ?>"><?= strtoupper($row['status']) ?></span>
                    <div class="actions">
                        <a href="edit_menu.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                        <a href="?hapus=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Yakin hapus <?= $row['nama_makanan'] ?>?')">Hapus</a>
                        <a href="?id=<?= $row['id'] ?>&status=<?= $row['status'] === 'tersedia' ? 'habis' : 'tersedia' ?>" class="ubah-status">
                            <?= $row['status'] === 'tersedia' ? 'Tandai Habis' : 'Tandai Tersedia' ?>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- DAFTAR SEMUA RESERVASI (KHUSUS ADMIN) -->
        <h1 style="margin-top: 80px;">Daftar Semua Reservasi</h1>

                <div class="menu-grid">
            <?php
            $res_query = "SELECT r.*, u.nama_lengkap, u.username 
                          FROM reservations r 
                          JOIN users u ON r.user_id = u.id 
                          ORDER BY r.tanggal DESC, r.jam DESC";
            $res_result = mysqli_query($koneksi, $res_query);

            if (mysqli_num_rows($res_result) == 0): ?>
                <p style="grid-column: 1/-1; text-align:center; font-size:18px;">Belum ada reservasi dari customer.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse; background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); border-radius:15px; overflow:hidden;">
                    <thead>
                        <tr style="background:rgba(255,255,255,0.2);">
                            <th style="padding:15px; text-align:left;">Customer</th>
                            <th style="padding:15px; text-align:left;">Tanggal</th>
                            <th style="padding:15px; text-align:left;">Jam</th>
                            <th style="padding:15px; text-align:left;">Jumlah Orang</th>
                            <th style="padding:15px; text-align:left;">Menu</th>
                            <th style="padding:15px; text-align:left;">Status</th>
                            <th style="padding:15px; text-align:left;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = mysqli_fetch_assoc($res_result)): ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.2);">
                                <td style="padding:15px;"><?= htmlspecialchars($res['nama_lengkap']) ?> (@<?= htmlspecialchars($res['username']) ?>)</td>
                                <td style="padding:15px;"><?= date('d M Y', strtotime($res['tanggal'])) ?></td>
                                <td style="padding:15px;"><?= date('H:i', strtotime($res['jam'])) ?></td>
                                <td style="padding:15px;"><?= $res['jumlah_orang'] ?> orang</td>
                                <td style="padding:15px;"><?= implode(', ', json_decode($res['menus'])) ?></td>
                                <td style="padding:15px;">
                                    <span style="padding:5px 15px; border-radius:20px; font-weight:700;
                                        <?= $res['status'] === 'pending' ? 'background:#FFA500; color:#000;' : '' ?>
                                        <?= $res['status'] === 'confirmed' ? 'background:#98FB98; color:#006400;' : '' ?>
                                        <?= $res['status'] === 'cancelled' ? 'background:#FF6B6B; color:white;' : '' ?>">
                                        <?= ucfirst($res['status']) ?>
                                    </span>
                                </td>
                                <td style="padding:15px;">
                                    <?php if ($res['status'] === 'pending'): ?>
                                        <a href="?confirm=<?= $res['id'] ?>" 
                                            style="background:#98FB98; color:#006400; padding:8px 16px; border-radius:8px; text-decoration:none; font-weight:600;" 
                                            onclick="return confirm('Yakin confirm reservasi <?= htmlspecialchars($res['nama_lengkap']) ?> pada <?= date('d M Y H:i', strtotime($res['tanggal'] . ' ' . $res['jam'])) ?>?')">
                                            Confirm
                                        </a>
                                    <?php elseif ($res['status'] === 'confirmed'): ?>
                                        <span style="color:#98FB98; font-weight:bold;">Sudah Dikonfirmasi</span>
                                    <?php elseif ($res['status'] === 'cancelled'): ?>
                                        <span style="color:#FF6B6B; font-weight:bold;">Dibatalkan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
</body>
</html>