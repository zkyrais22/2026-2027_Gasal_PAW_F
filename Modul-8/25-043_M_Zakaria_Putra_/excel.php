<?php
require 'includes/cek_login.php';
require 'koneksi.php';

$tanggal_mulai   = $_GET['tanggal_mulai']   ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

if (!tanggalValid($tanggal_mulai) || !tanggalValid($tanggal_selesai) || $tanggal_mulai > $tanggal_selesai) {
    die('Rentang tanggal tidak valid.');
}

$lap = ambilLaporan($conn, $tanggal_mulai, $tanggal_selesai);

// HTTP RESPONSE HEADER 
$namaFile = "rekap_penjualan_{$tanggal_mulai}_sd_{$tanggal_selesai}.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$namaFile\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html>
<head><meta charset="utf-8"></head>
<body>
    <h2>Rekap Laporan Penjualan</h2>
    <p>Periode: <?= $tanggal_mulai ?> sampai <?= $tanggal_selesai ?></p>

    <table border="1">
        <tr>
            <th>No</th>
            <th>Total Pendapatan</th>
            <th>Tanggal Transaksi</th>
        </tr>
        <?php $no = 1; foreach ($lap['rekap'] as $row): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['total_harian'] ?></td>
            <td><?= $row['tgl'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <table border="1">
        <tr><th>Jumlah Pelanggan Unik</th><td><?= $lap['pelanggan'] ?> Orang</td></tr>
        <tr><th>Total Pendapatan Keseluruhan</th><td><?= $lap['total'] ?></td></tr>
    </table>
</body>
</html>