<?php
// File ini untuk logout
session_start();     // Mulai sesi
session_destroy();   // Hapus semua data sesi (logout)

// Redirect ke halaman login
header('Location: login.php');
exit;
?>
