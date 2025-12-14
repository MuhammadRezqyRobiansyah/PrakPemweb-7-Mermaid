<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $jumlah_orang = (int)$_POST['jumlah_orang'];
    $menus = $_POST['menus'] ?? [];

    // Validasi
    if (empty($tanggal) || strtotime($tanggal) < strtotime('today')) {
        $error = "Tanggal harus hari ini atau setelahnya!";
    } elseif (empty($jam) || $jam < '18:00' || $jam > '22:00') {
        $error = "Jam hanya antara 18:00 - 22:00!";
    } elseif ($jumlah_orang < 1 || $jumlah_orang > 10) {
        $error = "Jumlah orang harus 1-10!";
    } elseif (count($menus) === 0) {
        $error = "Wajib pilih minimal 1 menu!";
    } else {
        // Simpan ke database
        $menus_json = json_encode($menus);
        $user_id = $_SESSION['user_id'];

        $query = "INSERT INTO reservations (user_id, tanggal, jam, jumlah_orang, menus, status) 
                  VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "issis", $user_id, $tanggal, $jam, $jumlah_orang, $menus_json);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Reservasi berhasil disimpan!";

            // Fake email seperti tugas
            $detail = "Tanggal: $tanggal, Jam: $jam, Jumlah orang: $jumlah_orang, Menu: " . implode(', ', $menus);
            sendReservationEmail($_SESSION, $detail);

            // Redirect ke my reservations
            header("Location: my_reservations.php");
            exit();
        } else {
            $error = "Gagal menyimpan reservasi.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil menu tersedia untuk checkbox
$menu_query = "SELECT nama_makanan FROM menu WHERE status = 'tersedia' ORDER BY nama_makanan";
$menu_result = mysqli_query($koneksi, $menu_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thalassa Deep - Reservasi</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #061E3F; color: #FFFFFF; min-height: 100vh; }
        header {
            background-color: #061E3F;
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .logo { font-family: 'Marko One', serif; font-size: 24px; color: #FFD700; letter-spacing: 1px; text-transform: uppercase; }
        nav { display: flex; gap: 40px; }
        nav a { text-decoration: none; color: #E0E0E0; font-size: 14px; transition: color 0.3s; }
        nav a:hover { color: #FFFFFF; }
        .reservation-hero {
            background: linear-gradient(rgba(6, 30, 63, 0.7), rgba(6, 30, 63, 0.7)), url('Pictures/bglautmermaid.jpeg');
            background-size: cover;
            background-position: center;
            padding: 120px 20px 60px 20px; /* Tambah 120px di atas */
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Ubah dari center jadi flex-start biar ga tengah vertikal terlalu */
            min-height: 80vh;
            position: relative;
        }
        .reservation-container { width: 100%; max-width: 900px; position: relative; z-index: 2; }
        .page-title {
            font-family: 'Marko One', serif;
            font-size: 42px;
            text-align: center;
            margin-bottom: 50px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            font-weight: 400;
            letter-spacing: 1px;
        }
        .reservation-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .form-group { margin-bottom: 25px; }
        .form-label {
            display: block;
            color: #FFFF00;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            background-color: rgba(118, 126, 150, 0.5);
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            color: #FFF;
            font-size: 16px;
            outline: none;
            transition: background 0.3s;
        }
        .form-input:focus { background-color: rgba(118, 126, 150, 0.7); }
        .menu-selection-title {
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            display: inline-block;
            padding-bottom: 5px;
        }
        .menu-options { display: flex; flex-direction: column; gap: 15px; }
        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
            position: relative;
            padding-left: 35px;
            user-select: none;
        }
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .checkmark {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 20px;
            background-color: #eee;
            border-radius: 4px;
        }
        .checkbox-container:hover input ~ .checkmark { background-color: #ccc; }
        .checkbox-container input:checked ~ .checkmark { background-color: #2196F3; }
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        .checkbox-container input:checked ~ .checkmark:after { display: block; }
        .checkbox-container .checkmark:after {
            left: 7px;
            top: 3px;
            width: 6px;
            height: 12px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
        .required-hint { color: #ff4d4d; font-size: 12px; margin-top: 10px; }
        .error { color: #ff6b6b; text-align: center; margin: 20px 0; font-size: 16px; }
        .success { color: #98fb98; text-align: center; margin: 20px 0; font-size: 18px; font-weight: bold; }
        .submit-btn {
            background-color: rgba(80, 90, 110, 0.8);
            color: #8FB8E6;
            font-weight: 700;
            font-size: 20px;
            padding: 15px;
            width: 100%;
            border: 2px solid #5D8AA8;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
            text-transform: uppercase;
            grid-column: span 2;
        }
        .submit-btn:hover {
            background-color: rgba(80, 90, 110, 1);
            color: white;
            border-color: #FFF;
        }
        .info-section { background-color: #FAFAFA; padding: 80px 20px; color: #333; }
        .info-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .info-card { background-color: #E0E0E0; border-radius: 30px; padding: 40px 30px; display: flex; flex-direction: column; gap: 20px; }
        .info-card h3 { font-weight: 800; font-size: 20px; margin-bottom: 10px; color: #000; }
        .info-item { display: flex; gap: 15px; align-items: flex-start; }
        .check-icon { font-size: 20px; font-weight: bold; color: #000; }
        .info-text-bold { font-weight: 700; margin-bottom: 5px; font-size: 14px; }
        .info-text-desc { font-size: 13px; line-height: 1.4; color: #333; }
        @media (max-width: 768px) {
            .reservation-form { grid-template-columns: 1fr; }
            .submit-btn { grid-column: span 1; }
            .info-grid { grid-template-columns: 1fr; }
            .page-title { font-size: 32px; }
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="reservation-hero">
        <div class="reservation-container">
            <h1 class="page-title">Form Reservasi Meja Restoran</h1>

            <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

            <form method="POST" class="reservation-form">
                <!-- Left Column -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Reservasi (*)</label>
                        <input type="date" class="form-input" name="tanggal" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Orang (*)</label>
                        <input type="number" class="form-input" name="jumlah_orang" min="1" max="10" required>
                        <div class="required-hint">Jumlah orang harus antara 1 sampai 10</div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Pilih Jam Kedatangan (*)</label>
                        <input type="time" class="form-input" name="jam" min="18:00" max="22:00" required>
                    </div>
                    <div class="form-group">
                        <div class="menu-selection-title">Pilih Menu Wajib</div>
                        <div class="menu-options">
                            <?php while ($m = mysqli_fetch_assoc($menu_result)): ?>
                                <label class="checkbox-container"><?= htmlspecialchars($m['nama_makanan']) ?>
                                    <input type="checkbox" name="menus[]" value="<?= htmlspecialchars($m['nama_makanan']) ?>">
                                    <span class="checkmark"></span>
                                </label>
                            <?php endwhile; ?>
                        </div>
                        <div class="required-hint">Wajib Pilih Minimal 1 Menu</div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Confirm Reservation</button>
            </form>
        </div>
    </div>

    <!-- Info Cards Section (sama persis) -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-card">
                <h3>Pembatalan Reservasi</h3>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div>
                        <div class="info-text-bold">Jika pembatalan dilakukan kurang dari 24 jam</div>
                        <div class="info-text-desc">tamu akan dikenakan biaya pembatalan.</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div>
                        <div class="info-text-bold">Jika tamu tidak hadir tanpa pemberitahuan (no-show)</div>
                        <div class="info-text-desc">deposit atau biaya minimum akan dianggap hangus.</div>
                    </div>
                </div>
            </div>
            <div class="info-card">
                <h3>Ketepatan Waktu</h3>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div><div class="info-text-bold">Toleransi keterlambatan adalah 10-15 menit.</div></div>
                </div>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div>
                        <div class="info-text-bold">Jika melewati waktu tersebut tanpa konfirmasi</div>
                        <div class="info-text-desc">reservasi dapat dibatalkan otomatis.</div>
                    </div>
                </div>
            </div>
            <div class="info-card">
                <h3>Batas Waktu Makan</h3>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div><div class="info-text-bold">Setiap reservasi memiliki durasi makan maksimal 90 menit.</div></div>
                </div>
                <div class="info-item">
                    <div class="check-icon">✓</div>
                    <div>
                        <div class="info-text-bold">Bila tamu melebihi batas waktu</div>
                        <div class="info-text-desc">tidak ada penambahan durasi.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
</body>
</html>