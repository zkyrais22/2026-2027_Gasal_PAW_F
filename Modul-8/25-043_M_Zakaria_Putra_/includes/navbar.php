<?php
$level = $_SESSION['level'];
$hal   = basename($_SERVER['PHP_SELF']);

$hal_master    = ['barang.php', 'supplier.php', 'pelanggan.php', 'user.php'];
$hal_transaksi = ['transaksi.php', 'detail.php'];
?>
<div class="navbar-app">
    <div class="kiri">
        <a class="brand" href="index.php">Sistem Penjualan</a>
        <ul>
            <li class="<?= $hal == 'index.php' ? 'aktif' : '' ?>"><a href="index.php">Home</a></li>

            <?php if ($level == 1) { ?>
            <li class="dropdown <?= in_array($hal, $hal_master) ? 'aktif' : '' ?>">
                <a href="#" class="caret" onclick="return false">Data Master</a>
                <div class="dropdown-menu">
                    <a href="barang.php">Data Barang</a>
                    <a href="supplier.php">Data Supplier</a>
                    <a href="pelanggan.php">Data Pelanggan</a>
                    <a href="user.php">Data User</a>
                </div>
            </li>
            <?php } ?>

            <li class="<?= in_array($hal, $hal_transaksi) ? 'aktif' : '' ?>"><a href="transaksi.php">Transaksi</a></li>
            <li class="<?= $hal == 'laporan.php' ? 'aktif' : '' ?>"><a href="laporan.php">Laporan</a></li>
        </ul>
    </div>

    <ul class="kanan">
        <li class="dropdown">
            <a href="#" class="caret" onclick="return false"><?= htmlspecialchars($_SESSION['nama']) ?></a>
            <div class="dropdown-menu">
                <a href="logout.php">Logout</a>
            </div>
        </li>
    </ul>
</div>
