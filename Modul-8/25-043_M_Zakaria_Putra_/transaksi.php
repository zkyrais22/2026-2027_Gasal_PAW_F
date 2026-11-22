<?php
include 'includes/cek_login.php';
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $waktu = $_POST['waktu_transaksi'];
    $ket   = $_POST['keterangan'];
    $pel   = $_POST['pelanggan_id'];

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id)
         VALUES (?, ?, 0, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $waktu, $ket, $pel);
    mysqli_stmt_execute($stmt);

    $id_baru = mysqli_insert_id($koneksi);
    header("Location: detail.php?id=" . $id_baru);
    exit;
}

$transaksi = mysqli_query($koneksi,
    "SELECT t.id, t.waktu_transaksi, t.keterangan, t.total, p.nama
     FROM transaksi t
     INNER JOIN pelanggan p ON t.pelanggan_id = p.id
     ORDER BY t.id DESC");

$pelanggan = mysqli_query($koneksi, "SELECT id, nama FROM pelanggan ORDER BY nama");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <h1>Data Transaksi</h1>

    <h2>Tambah Transaksi Baru</h2>
    <form method="post">
        <label>Tanggal</label>
        <input type="date" name="waktu_transaksi" value="<?= date('Y-m-d') ?>" required>

        <label>Pelanggan</label>
        <select name="pelanggan_id" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                <option value="<?= e($p['id']) ?>"><?= e($p['nama']) ?></option>
            <?php } ?>
        </select>

        <label>Keterangan</label>
        <input type="text" name="keterangan">

        <button type="submit" name="simpan">Simpan &amp; Isi Detail</button>
    </form>

    <h2>Daftar Transaksi</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Keterangan</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($transaksi)) { ?>
        <tr>
            <td><?= e($row['id']) ?></td>
            <td><?= e($row['waktu_transaksi']) ?></td>
            <td><?= e($row['nama']) ?></td>
            <td><?= e($row['keterangan']) ?></td>
            <td class="kanan">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
            <td><a href="detail.php?id=<?= e($row['id']) ?>">Lihat Detail</a></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>