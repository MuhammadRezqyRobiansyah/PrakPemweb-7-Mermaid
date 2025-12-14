<?php

function uploadFoto($file) {

    $error = '';
    $nama_file = '';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Error upload file.";
    } elseif ($file['size'] > 2 * 1024 * 1024) { // < 2MB
        $error = "Ukuran foto maksimal 2MB.";
    } elseif (!in_array($file['type'], ['image/jpeg', 'image/png'])) {
        $error = "Foto harus JPG atau PNG.";
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nama_file = time() . '_' . rand(1000, 9999) . '.' . $ext;
        $tujuan = 'uploads/' . $nama_file;

        if (move_uploaded_file($file['tmp_name'], $tujuan)) {
            return $nama_file; // sukses, return nama file
        } else {
            $error = "Gagal memindahkan file.";
        }
    }

    return $error ? $error : $nama_file;
}

function sendReservationEmail($user, $detail) {
    echo "Email terkirim ke " . htmlspecialchars($user['email']) . "<br>";
    echo "Detail reservasi: " . htmlspecialchars($detail) . "<br>";
}
?>