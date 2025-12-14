<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        
        header {
            background-color: #061E3F;
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: padding 0.3s, background 0.3s, box-shadow 0.3s;
        }
        header.scrolled {
            padding: 16px 40px;
            background-color: rgba(6, 30, 63, 0.95);
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .logo {
            font-family: 'Marko One', serif;
            font-weight: 400;
            font-size: 24px;
            color: #FFD700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        nav {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        nav a {
            color: #E0E0E0;
            text-decoration: none;
            font-weight: 400;
            font-size: 14px;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #FFFFFF;
        }
        .admin-badge {
            background-color: #5D8AA8;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header id="mainHeader">
        <div class="logo">THALASSA DEEP</div>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <!-- Admin: hanya Dashboard + Logout -->
                    <a href="admin_dashboard.php">Admin Dashboard</a>
                    <span class="admin-badge">ADMIN</span>
                    <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?>)</a>
                <?php else: ?>
                    <!-- Customer: Menu + Reservasi + My Reservations + Logout (TANPA Home) -->
                    <a href="menu.php">Menu</a>
                    <a href="reservasi.php">Reservasi</a>
                    <a href="my_reservations.php">My Reservations</a>
                    <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?>)</a>
                <?php endif; ?>
            <?php else: ?>
                <!-- Belum login: Home + Login + Register -->
                <a href="home2.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <script>
        window.addEventListener('scroll', () => {
            const header = document.getElementById('mainHeader');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>