<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Daftar section
$sections = [
    'signature' => ['kategori' => 'appetizer', 'title' => 'Signature Menu', 'bg' => 'signature menu background.jpeg'],
    'main' => ['kategori' => 'main_course', 'title' => 'Main Course', 'bg' => 'main course background.jpeg'],
    'dessert' => ['kategori' => 'dessert', 'title' => 'Dessert', 'bg' => 'dessert background.jpeg'],
    'beverage' => ['kategori' => 'drink', 'title' => 'Signature Drinks', 'bg' => 'signature menu drink background.jpeg']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thalassa Deep - Menu</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #061E3F;
            color: white;
            min-height: 100vh;
            overflow-x: hidden;
        }
        /* FILTER BAR */
        .filter-container {
            background-color: #FFFFFF;
            padding: 15px 0;
            text-align: center;
            position: relative;
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background-color: #6D5C58;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 3px solid transparent;
        }

        .filter-btn.active {
            background-color: #4A3B38;
            border: 4px solid #FFD700 !important;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        }

        .filter-btn:hover {
            background-color: #4A3B38;
            border: 4px solid #FFD700;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
        }

        .hidden {
            display: none;
        }
        /* Menu Sections */
        .menu-wrapper {
            background-color: #061E3F;
            width: 100%;
        }
        
        .menu-section {
            padding: 60px 20px;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            width: 100%;
            box-sizing: border-box;
            background-repeat: no-repeat;
        }
        .section-title {
            text-align: center;
            font-family: 'Marko One', serif;
            color: #FFD700;
            font-size: 26px;
            margin: 0 0 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        .section-title::before, .section-title::after {
            content: '🔱';
            font-size: 24px;
            color: #5DA998;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        @media (max-width: 1024px) { .menu-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .menu-grid { grid-template-columns: 1fr; } }
        .menu-card {
            background-color: #E8E8E8;
            border-radius: 24px;
            padding: 12px;
            text-align: center;
            color: #333;
            transition: transform 0.3s;
            position: relative;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .menu-card:hover { transform: translateY(-8px); }
        .menu-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .menu-name {
            font-family: 'Marko One', serif;
            font-weight: 500;
            font-size: 16px;
            margin-bottom: 5px;
            line-height: 1.2;
            color: #000;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-price {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 12px;
        }
        .order-btn {
            background-color: #3E2C29;
            color: white;
            border: none;
            width: 100%;
            padding: 10px 0;
            border-radius: 12px;
            font-weight: 400;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .order-btn:hover {
            background-color: #5D4037;
        }
        /* Footer CTA */
        .menu-footer {
            background-color: #E6E6E6;
            color: #333;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        .footer-content h3 {
            font-family: 'Marko One', serif;
            font-size: 28px;
            margin-bottom: 10px;
            color: #061E3F;
        }
        .footer-content p {
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .reserv-btn {
            background-color: #0F3460;
            color: white;
            padding: 14px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .reserv-btn:hover {
            background-color: #1a4d85;
            transform: translateY(-2px);
        }
        .footer-mermaid-img {
            max-width: 250px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }
        .hidden { display: none; }
        .separator-container {
            position: relative;
            height: 0;
            z-index: 10;
        }
        .separator-fish {
            position: absolute;
            right: 5%;
            top: -50px;
            width: 150px;
            animation: swim 4s ease-in-out infinite alternate;
        }
        .separator-mermaid {
            position: absolute;
            left: 0;
            top: -80px;
            width: 200px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes swim {
            0% { transform: translateX(0) rotate(0deg); }
            100% { transform: translateX(-10px) rotate(5deg); }
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="filter-container">
        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterMenu('all')">All Dishes</button>
            <button class="filter-btn" onclick="filterMenu('signature')">Signature Menu</button>
            <button class="filter-btn" onclick="filterMenu('main')">Main Course</button>
            <button class="filter-btn" onclick="filterMenu('dessert')">Dessert</button>
            <button class="filter-btn" onclick="filterMenu('beverage')">Signature Drinks</button>
        </div>
    </div>

    <div class="menu-wrapper">
        <?php
        foreach ($sections as $key => $sec) {
            $kat = $sec['kategori'];
            $title = $sec['title'];
            $bg = $sec['bg'];

            $query = "SELECT * FROM menu WHERE kategori = ? AND status = 'tersedia' ORDER BY id";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "s", $kat);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0):
        ?>
                <div id="sect-<?= $key ?>" class="menu-section" style="background-image: linear-gradient(rgba(6, 30, 63, 0.4), rgba(6, 30, 63, 0.4)), url('Pictures/<?= $bg ?>');">
                    <h2 class="section-title"><?= $title ?> - Thalassa Deep</h2>
                    <div class="menu-grid">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="menu-card">
                                <img src="Pictures/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_makanan']) ?>">
                                <div class="menu-content">
                                    <div class="menu-name"><?= htmlspecialchars($row['nama_makanan']) ?></div>
                                    <div class="menu-price">Rp. <?= number_format($row['harga'], 0, ',', '.') ?></div>
                                    <button class="order-btn" onclick="orderItem('<?= htmlspecialchars($row['nama_makanan']) ?>')">Pesan Sekarang</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <?php 
                if ($key === 'signature'): ?>
                    <div class="separator-container">
                        <img src="Pictures/ikan kuning.jpeg" class="separator-fish" alt="Ikan Kuning">
                    </div>
                <?php elseif ($key === 'main'): ?>
                    <div class="separator-container">
                        <img src="Pictures/mermaid dan ikan.jpeg" class="separator-mermaid" alt="Mermaid">
                    </div>
                <?php endif; ?>

            <?php 
            endif;
            mysqli_stmt_close($stmt);
        }
        ?>
    </div>

    <div class="menu-footer">
        <div class="footer-content">
            <h3>Siap Menikmati Keajaiban Kuliner Kami?</h3>
            <p>Pesan Meja Anda dan Bersantap yang Tak Terlupakan Di Bawah Laut</p>
            <button class="reserv-btn" onclick="window.location.href='reservasi.php'">Buat Reservasi</button>
        </div>
        <img src="Pictures/mermaid reservasi.jpeg" class="footer-mermaid-img" alt="Mermaid">
    </div>

    <script>
        function filterMenu(category) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const sections = document.querySelectorAll('.menu-section');
            sections.forEach(sec => {
                if (category === 'all') {
                    sec.classList.remove('hidden');
                } else {
                    if (sec.id === 'sect-' + category) {
                        sec.classList.remove('hidden');
                    } else {
                        sec.classList.add('hidden');
                    }
                }
            });
        }

        function orderItem(itemName) {
            alert(itemName + ' telah ditambahkan ke pesanan Anda!');
        }
    </script>
    <?php require_once 'footer.php'; ?>
</body>
</html>