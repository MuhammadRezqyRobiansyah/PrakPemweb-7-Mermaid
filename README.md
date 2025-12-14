# Thalassa Deep 🌊🧜‍♀️

## Deskripsi Aplikasi  
Thalassa Deep adalah website restoran mewah bertema bawah laut yang hanya bisa diakses oleh tamu terpilih. Restoran ini menyajikan hidangan laut premium dan dilayani langsung oleh putri duyung asli. Pengunjung harus mendaftar dan login untuk melihat menu rahasia serta melakukan reservasi meja.  
Aplikasi ini dibuat untuk memenuhi tugas **Praktikum Pemrograman Web - Kelompok 7 Mermaid**.

## Fitur Aplikasi 🍽️

1. Register dan Login dengan session PHP (validasi unik username/email, password di-hash).
2. Role-based access: **Customer** dan **Admin** dengan tampilan berbeda.
3. Menampilkan menu rahasia dalam grid (foto, nama, harga, kategori) — hanya menu yang "tersedia".
4. Reservasi meja: pilih tanggal, jam (18:00–22:00), jumlah orang (1–10), minimal 1 menu.
5. My Reservations: lihat daftar reservasi sendiri + batalkan jika masih pending.
6. Admin Dashboard: CRUD menu lengkap (tambah, edit, hapus, ubah status, upload foto).
7. Admin dapat melihat semua reservasi user + confirm reservasi pending.
8. Konfirmasi reservasi via fake email (sesuai tugas).

## Teknologi yang Digunakan 🛠️

1. HTML
2. CSS
3. JavaScript
4. PHP
5. MySQL
6. Visual Studio Code
7. XAMPP / Laragon
8. Git & GitHub

## Struktur Project 📂

```
PrakPemweb-7-Mermaid/
├── Pictures/                # Folder gambar (background, foto menu default, mermaid, dll)
├── screenshots/             # (Opsional) Screenshot untuk README
├── admin_dashboard.php      # Dashboard admin (CRUD menu + lihat reservasi)
├── edit_menu.php            # Form edit menu
├── tambah_menu.php          # Form tambah menu
├── functions.php            # Fungsi uploadFoto() & sendReservationEmail()
├── header.php               # Header dinamis sesuai role user
├── footer.php               # Footer semua halaman
├── home2.php                # Landing page / Home
├── koneksi.php              # Koneksi database
├── login.php                # Halaman login
├── logout.php               # Proses logout
├── menu.php                 # Halaman menu customer (grid + filter kategori)
├── my_reservations.php      # Daftar reservasi customer
├── register.php             # Halaman registrasi
├── reservasi.php            # Form reservasi meja
├── .gitignore               # File yang diabaikan oleh git
└── README.md                # Deskripsi proyek ini
```

## Cara Install 👾

1. Clone repository ini:
   ```bash
   git clone https://github.com/MuhammadRezqyRobiansyah/PrakPemweb-7-Mermaid.git
2. Pindahkan folder project ke dalam folder htdocs (XAMPP) atau www (Laragon).
3. Jalankan Apache dan MySQL di XAMPP/Laragon.
4. Buat database bernama thalassa_deep di phpMyAdmin.
5. Import struktur tabel berikut ke database thalassa_deep:
   ```
   CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    no_hp VARCHAR(20),
    role ENUM('customer', 'admin') DEFAULT 'customer'
   );

   CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_makanan VARCHAR(100) NOT NULL,
    kategori ENUM('appetizer', 'main_course', 'dessert', 'drink'),
    harga INT NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(100) DEFAULT 'default.jpg',
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia'
   );

   CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    tanggal DATE,
    jam TIME,
    jumlah_orang INT,
    menus JSON,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );

6. Buka browser: http://localhost/[nama-folder-project]/home2.php

## Login Admin Default
   
Buat manual di database atau register lalu ubah role jadi 'admin':
- Username: admin
- Password: admin123

## Kontributor 👷‍♂️

1. Zahratul Askia [NIM H1D024016]
2. Muhammad Rezqy Robiansyah [NIM H1D024129]
3. Alifvia Putri Dewani [NIM H1D024131]

# Terima kasih telah mengunjungi Thalassa Deep! 🍤🌊
