<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: menu.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmpassword'] ?? '';

    if (empty($fullname) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
        $error = "Semua field wajib diisi!";
    } elseif ($password !== $confirm) {
        $error = "Password dan konfirmasi tidak sama!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $query = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username atau email sudah terdaftar!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) 
                       VALUES (?, ?, ?, ?, ?, 'customer')";
            $stmt2 = mysqli_prepare($koneksi, $insert);
            mysqli_stmt_bind_param($stmt2, "sssss", $username, $hashed_password, $fullname, $email, $phone);

            if (mysqli_stmt_execute($stmt2)) {
                $success = "Registrasi berhasil! Sedang mengarahkan ke menu...";

                $query_login = "SELECT id, username, nama_lengkap, role FROM users WHERE username = ?";
                $stmt3 = mysqli_prepare($koneksi, $query_login);
                mysqli_stmt_bind_param($stmt3, "s", $username);
                mysqli_stmt_execute($stmt3);
                $result = mysqli_stmt_get_result($stmt3);
                $user = mysqli_fetch_assoc($result);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                echo '<script>setTimeout(function(){ window.location.href = "menu.php"; }, 2000);</script>';
            } else {
                $error = "Gagal menyimpan data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thalassa Deep - Register</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Marko+One&display=swap');
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #061E3F;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('Pictures/bglautmermaid.jpeg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 100px;
            padding-bottom: 60px;
        }
        .register-container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
        }
        .register-container h2 {
            font-family: 'Marko One', serif;
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 40px;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }
        .error {
            color: #FF6B6B;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .success {
            color: #98FB98;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
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
        input {
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
            margin-top: 10px;
            padding: 16px;
            border-radius: 12px;
            border: 3px solid #5DA998;
            background-color: rgba(60, 70, 90, 0.8);
            color: #9cdbd0;
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            grid-column: span 2;
        }
        button:hover {
            background-color: #5DA998;
            color: #061E3F;
        }
        .login-text {
            grid-column: span 2;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #E0E0E0;
        }
        .login-text a {
            color: #FFD700;
            text-decoration: none;
            font-weight: 600;
        }
        .login-text a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            form {
                grid-template-columns: 1fr;
            }
            button, .login-text {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>

    <?php require_once 'header.php'; ?>

    <div class="main-content">
        <div class="register-container">
            <h2>JOIN THE SELECTED GUESTS</h2>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirmpassword" required>
                </div>

                <button type="submit">CREATE ACCOUNT</button>

                <p class="login-text">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </form>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>

</body>
</html>