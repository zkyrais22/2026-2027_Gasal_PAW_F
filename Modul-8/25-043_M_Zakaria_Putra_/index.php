<?php
include 'includes/cek_login.php';
include 'koneksi.php';

$jabatan = ($_SESSION['level'] == 1) ? "Owner" : "Kasir";

$ringkas = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS jumlah, COALESCE(SUM(total), 0) AS pendapatan FROM transaksi"));
$jml_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM barang"));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <?php if (($_GET['pesan'] ?? '') == 'akses') { ?>
        <p class="gagal">Anda tidak memiliki hak akses ke halaman tersebut.</p>
    <?php } ?>

    <h1>Selamat datang, <?= e($_SESSION['nama']) ?></h1>
    <p>Anda login sebagai <b><?= $jabatan ?></b>.</p>

    <div class="card-ringkas">Jumlah Transaksi <b><?= e($ringkas['jumlah']) ?></b></div>
    <div class="card-ringkas">Total Pendapatan <b>Rp <?= number_format($ringkas['pendapatan'], 0, ',', '.') ?></b></div>
    <div class="card-ringkas">Jumlah Barang <b><?= e($jml_barang['jumlah']) ?></b></div>
</div>
</body>
</html>
