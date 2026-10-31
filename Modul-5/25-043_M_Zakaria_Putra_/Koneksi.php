<?php 
// Koneksi ke database LARAGON : root, pw kosong
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost","root","","penjualan");
$conn->set_charset("utf8mb4");
?>