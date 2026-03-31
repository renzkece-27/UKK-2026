<?php
// File ini untuk koneksi ke database
// Ubah sesuai pengaturan MySQL kamu

$host = 'localhost';
$user = 'root';
$pass = '';
$nama_db = 'ukk_rendy';

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $nama_db);

// Jika gagal konek, tampilkan pesan error
if (!$conn) {
    die('Gagal konek ke database!');
}
?>
