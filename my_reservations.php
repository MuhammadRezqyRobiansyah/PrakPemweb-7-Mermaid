<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle batalkan reservasi
if (isset($_GET['batalkan'])) {
    $res_id = (int)$_GET['batalkan'];
    $cek = mysqli_query($koneksi, "SELECT id FROM reservations WHERE id = $res_id AND user_id = $user_id AND status = 'pending'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE reservations SET status = 'cancelled' WHERE id = $res_id");
    }
    header("Location: my_reservations.php");
    exit();
}

// Ambil semua reservasi user ini
$query = "SELECT r.*, u.nama_lengkap FROM reservations r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.user_id = $user_id 
          ORDER BY r.tanggal DESC, r.jam DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thalassa Deep - My Reservations</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(6, 30, 63, 0.85), rgba(6, 30, 63, 0.85)), url('Pictures/bglautmermaid.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #FFFFFF;
            min-height: 100vh;
        }
        .my-reservation-hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 120px; /* space untuk header sticky */
            position: relative;
        }
        .page-title {
            font-family: 'Marko One', serif;
            font-size: 36px;
            color: #FFD700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 60px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .table-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 60px;
        }
        .reservation-table {
            width: 100%;
            border-collapse: collapse;
        }
        .reservation-table th,
        .reservation-table td {
            padding: 20px;
            text-align: center;
            font-size: 18px;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .reservation-table th:last-child,
        .reservation-table td:last-child {
            border-right: none;
        }
        .reservation-table tr:last-child td {
            border-bottom: none;
        }
        .reservation-table th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
            text-transform: capitalize;
        }
        .btn-cancel {
            background-color: #D32F2F;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-cancel:hover {
            background-color: #B71C1C;
        }
        .no-reservation {
            text-align: center;
            padding: 80px 20px;
            font-size: 24px;
            color: #E0E0E0;
        }
        .no-reservation a {
            color: #FFD700;
            text-decoration: none;
        }
        .no-reservation a:hover {
            text-decoration: underline;
        }
        .status-pending { color: #FFA500; font-weight: bold; }
        .status-confirmed { color: #98FB98; font-weight: bold; }
        .status-cancelled { color: #FF6B6B; font-weight: bold; }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="my-reservation-hero">
        <h1 class="page-title">DAFTAR RESERVASI SAYA</h1>

        <div class="table-container">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="no-reservation">
                    Belum ada reservasi.<br><br>
                    <a href="reservasi.php">Buat Reservasi Sekarang</a>
                </div>
            <?php else: ?>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Jumlah Orang</th>
                            <th>Menu Pilihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= date('d F Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jam'])) ?></td>
                                <td><?= $row['jumlah_orang'] ?> orang</td>
                                <td><?= implode('<br>', json_decode($row['menus'])) ?></td>
                                <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                                <td>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="?batalkan=<?= $row['id'] ?>" class="btn-cancel" onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">Batalkan Reservasi</a>
                                    <?php else: ?>
                                        -
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