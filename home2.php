<?php
session_start();
require_once 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thalassa Deep</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #061E3F;
            color: #FFFFFF;
            min-height: 100vh;
            line-height: 1.5;
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; transition: opacity 0.3s; }
        a:hover { opacity: 0.8; }
        img { display: block; max-width: 100%; }
        button { cursor: pointer; border: none; background: none; font-family: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        /* HERO */
        .hero {
            position: relative;
            width: 100%;
            height: 90vh;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('bglautmermaid.jpeg');
            background-size: cover;
            background-position: center;
            padding-left: 8%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        .hero-content {
            max-width: 600px;
            margin-top: -50px;
        }
        .hero h1 {
            font-family: 'Marko One', serif;
            font-size: 48px;
            font-weight: 400;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        .hero h2 {
            font-family: 'Marko One', serif;
            font-size: 48px;
            font-weight: 400;
            margin-bottom: 24px;
            line-height: 1.2;
        }
        .title-small {
            font-size: 32px;
            display: block;
            margin-bottom: 8px;
        }
        .hero p {
            font-size: 16px;
            margin-bottom: 32px;
            color: #E0E0E0;
            max-width: 450px;
            line-height: 1.6;
        }
        .btn-book {
            background-color: #5B9BFF;
            color: #000;
            font-weight: 600;
            font-size: 18px;
            padding: 12px 24px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
        }
        .btn-book:hover {
            background-color: #4884DD;
            transform: translateY(-2px);
        }
        /* GALLERY */
        .gallery-section {
            padding: 60px 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
            background-color: #0B1C38;
        }
        .gallery-img {
            width: 250px;
            height: 350px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5);
        }
        /* FEATURES */
        .features-container {
            background-color: #082855;
            margin: 40px 60px;
            border-radius: 20px;
            padding: 60px;
            text-align: center;
        }
        .features-container h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 50px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .icon-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .feature-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            width: 80%;
            line-height: 1.4;
        }
        .feature-desc {
            font-size: 12px;
            color: #B0C4DE;
            line-height: 1.5;
            padding: 0 10px;
        }
        /* FOOTER */
        footer {
            background-color: #FFFFFF;
            color: #333;
            padding: 60px;
            font-size: 12px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .footer-left {
            max-width: 400px;
        }
        .footer-left h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #000;
        }
        .footer-left p {
            color: #666;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .social-icons {
            display: flex;
            gap: 16px;
        }
        .social-icons svg {
            width: 20px;
            height: 20px;
            fill: #888;
            cursor: pointer;
            transition: fill 0.2s, transform 0.2s;
        }
        .social-icons svg:hover {
            fill: #000;
            transform: scale(1.2);
        }
        .footer-right {
            display: flex;
            gap: 80px;
        }
        .footer-col h5 {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #000;
        }
        .footer-col p, .footer-col a {
            display: block;
            color: #666;
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .features-grid { grid-template-columns: 1fr 1fr; }
            .gallery-section { flex-wrap: wrap; }
            .footer-content { flex-direction: column; gap: 40px; }
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <span class="title-small">Thalassa Deep</span>
            <h2>Where the Ocean Comes Alive</h2>
            <p>Rasakan sensasi makan di ruang bawah laut dengan panorama kerajaan laut yang misterius.</p>
            <a href="login.php" class="btn-book">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Enter Restaurant
            </a>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section class="gallery-section">
        <img src="Pictures/lobsterlemon.jpeg" class="gallery-img" alt="Lobster Dish">
        <img src="Pictures/seafoodplatter.jpeg" class="gallery-img" alt="Seafood Platter">
        <img src="Pictures/rotiudang.jpeg" class="gallery-img" alt="Bread Bowl Seafood">
        <img src="Pictures/lobsterbesar.jpeg" class="gallery-img" alt="Large Lobster">
    </section>

    <!-- FEATURES SECTION -->
    <div class="container">
        <section class="features-container">
            <h3>Keunggulan Thalassa Deep – Underwater Restaurant Experience</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="icon-wrapper">
                        <img src="Pictures/tetesanair.jpeg" alt="Icon">
                    </div>
                    <div class="feature-title">Real Underwater Dining Atmosphere</div>
                    <div class="feature-desc">Makan langsung di bawah laut dengan cahaya biru dan pemandangan kota Atlantis.</div>
                </div>
                <div class="feature-item">
                    <div class="icon-wrapper">
                        <img src="Pictures/gelembung.jpeg" alt="Icon">
                    </div>
                    <div class="feature-title">Immersive 360° Ocean View</div>
                    <div class="feature-desc">Kubah kaca transparan memberi pandangan penuh ke ikan, ubur-ubur, dan dunia laut.</div>
                </div>
                <div class="feature-item">
                    <div class="icon-wrapper">
                        <img src="Pictures/cipratan.jpeg" alt="Icon">
                    </div>
                    <div class="feature-title">Deep-Themed Culinary</div>
                    <div class="feature-desc">Menu dan plating terinspirasi dari estetika dasar laut dan kerajaan Thalassa.</div>
                </div>
                <div class="feature-item">
                    <div class="icon-wrapper">
                        <img src="Pictures/embun.jpeg" alt="Icon">
                    </div>
                    <div class="feature-title">Calm & Healing Ambience</div>
                    <div class="feature-desc">Suara ombak dan cahaya laut menciptakan suasana makan yang tenang dan menenangkan.</div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once 'footer.php'; ?>
</body>
</html>