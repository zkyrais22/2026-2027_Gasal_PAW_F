<?php
// dipasang paling atas di semua halaman yang butuh login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// supaya setelah logout, tombol Back ga nampilin halaman lama 
header("Cache-Control: no-store, no-cache, must-revalidate");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
