<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: menu.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        // Cari user di database
        $query = "SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Login sukses
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                $_SESSION['role'] = $row['role'];

                // Redirect sesuai role
                if ($row['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: menu.php");
                }
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thalassa Deep - Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #061E3F;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('bglautmermaid.jpeg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        nav {
            background-color: rgba(6, 30, 63, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 40px;
            position: fixed;
            top: 0;
            width: 100%;
            box-sizing: border-box;
            z-index: 100;
        }
        .logo {
            font-family: 'Marko One', serif;
            font-weight: 400;
            font-size: 24px;
            color: #FFD700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        nav div {
            display: flex;
            gap: 30px;
        }
        nav a {
            color: #E0E0E0;
            text-decoration: none;
            font-weight: 400;
            font-size: 14px;
            transition: color 0.3s;
        }
        nav a:hover {
            color: white;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 80px;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }
        .login-container h2 {
            font-family: 'Marko One', serif;
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 40px;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .error {
            color: #FF6B6B;
            margin-bottom: 20px;
            font-size: 14px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 25px;
            text-align: left;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        label {
            color: #FFD700;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            padding: 15px;
            border-radius: 12px;
            background-color: rgba(85, 95, 120, 0.9);
            border: 3px solid #5DA998;
            color: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }
        input::placeholder {
            color: #ccc;
        }
        input:focus {
            background-color: rgba(100, 110, 140, 1);
            box-shadow: 0 0 10px rgba(93, 169, 152, 0.4);
        }
        button {
            margin-top: 20px;
            padding: 15px;
            border-radius: 12px;
            border: 3px solid #5DA998;
            background-color: rgba(60, 70, 90, 0.8);
            color: #9cdbd0;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        button:hover {
            background-color: #5DA998;
            color: #061E3F;
        }
        .register-text {
            margin-top: 30px;
            font-size: 14px;
            color: #E0E0E0;
        }
        .register-text a {
            color: #FFD700;
            text-decoration: none;
            font-weight: 600;
        }
        .register-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php require_once 'header.php'; ?>

    <nav>
        <div class="logo">THALASSA DEEP</div>
        <div>
            <a href="home2.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="login-container">
            <h2>WELCOME BACK</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit">ENTER RESTAURANT</button>
            </form>

            <p class="register-text">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>