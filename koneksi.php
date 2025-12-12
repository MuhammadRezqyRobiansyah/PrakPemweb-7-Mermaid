<?php
// KONFIGURASI DATABASE
$host = "localhost";
$user = "root";       
$pass = "";
$db = "thalassa_deep"; 

// MEMBUAT KONEKSI (MySQLi)
$koneksi = new mysqli($host, $user, $pass, $db);

// CEK KONEKSI 
if ($koneksi->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}
// Set character set agar mendukung emoji dan bahasa non-latin (opsional)
$koneksi->set_charset("utf8mb4");

?>