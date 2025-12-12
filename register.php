<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Ambil data dari form
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $konfirm_password = $_POST['konfirm_password'];
    $nama_lengkap = $koneksi->real_escape_string($_POST['nama_lengkap']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $no_hp = $koneksi->real_escape_string($_POST['no_hp']);
    
    $errors = [];

    // 2. Lakukan Validasi Ketat
    if (empty($username) || empty($password) || empty($konfirm_password) || empty($nama_lengkap) || empty($email)) {
        $errors[] = "Semua field wajib diisi.";
    }

    if ($password !== $konfirm_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password harus minimal 6 karakter.";
    }

    // Cek Unik Username dan Email
    if (empty($errors)) {
        $stmt_check = $koneksi->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $errors[] = "Username atau Email sudah terdaftar.";
        }
        $stmt_check->close();
    }

    // 3. Jika tidak ada error, simpan data
    if (empty($errors)) {
        // Hashing Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';

        $stmt = $koneksi->prepare("INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashed_password, $nama_lengkap, $email, $no_hp, $role);
        
        if ($stmt->execute()) {
            // Pendaftaran berhasil, arahkan ke halaman login
            header("Location: login.php?status=success");
            exit;
        } else {
            $errors[] = "Pendaftaran gagal: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun | Thalassa Deep</title>
    </head>
<body>

    <h2>Daftar Akun Baru - Thalassa Deep</h2>
    
    <?php if (!empty($errors)): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required><br><br>

        <label for="password">Password (min. 6 karakter):</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="konfirm_password">Konfirmasi Password:</label><br>
        <input type="password" id="konfirm_password" name="konfirm_password" required><br><br>
        
        <label for="nama_lengkap">Nama Lengkap:</label><br>
        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap ?? ''); ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br><br>

        <label for="no_hp">Nomor HP (Opsional):</label><br>
        <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($no_hp ?? ''); ?>"><br><br>
        
        <input type="submit" value="Daftar Akun">
    </form>
    
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>

</body>
</html>