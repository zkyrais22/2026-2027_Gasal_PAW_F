<?php
include 'includes/cek_login.php';
include 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$pesan = "";

// Ambil Data Master
$stmt = mysqli_prepare($koneksi,
    "SELECT t.*, p.nama AS nama_pelanggan
     FROM transaksi t
     INNER JOIN pelanggan p ON t.pelanggan_id = p.id
     WHERE t.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$master = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$master) {
    die("Transaksi tidak ditemukan. <a href='transaksi.php'>Kembali</a>");
}

// Proses Tambah Detail
if (isset($_POST['tambah'])) {
    $barang_id = (int) $_POST['barang_id'];
    $qty       = (int) $_POST['qty'];

    if ($barang_id <= 0 || $qty <= 0) {
        $pesan = "Pilih barang dan isi qty minimal 1.";
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT 1 FROM transaksi_detail WHERE transaksi_id = ? AND barang_id = ?");
        mysqli_stmt_bind_param($cek, "ii", $id, $barang_id);
        mysqli_stmt_execute($cek);
        $sudah_ada = mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0;

        if ($sudah_ada) {
            $pesan = "Barang tersebut sudah ada di transaksi ini.";
        } else {
            $h = mysqli_prepare($koneksi, "SELECT harga FROM barang WHERE id = ?");
            mysqli_stmt_bind_param($h, "i", $barang_id);
            mysqli_stmt_execute($h);
            $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($h));

            if (!$barang) {
                $pesan = "Barang tidak ditemukan.";
            } else {
                $harga = (int) $barang['harga'];

                $ins = mysqli_prepare($koneksi,
                    "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty)
                     VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($ins, "iiii", $id, $barang_id, $harga, $qty);
                mysqli_stmt_execute($ins);

                // Hitung ulang total transaksi otomatis
                $upd = mysqli_prepare($koneksi,
                    "UPDATE transaksi
                     SET total = (SELECT SUM(harga * qty)
                                  FROM transaksi_detail
                                  WHERE transaksi_id = ?)
                     WHERE id = ?");
                mysqli_stmt_bind_param($upd, "ii", $id, $id);
                mysqli_stmt_execute($upd);

                header("Location: detail.php?id=" . $id . "&ok=1");
                exit;
            }
        }
    }
}

if (isset($_GET['ok'])) {
    $pesan = "Detail transaksi berhasil ditambahkan dan total diperbarui.";
}

// Ambil Data Detail
$stmt = mysqli_prepare($koneksi,
    "SELECT d.barang_id, b.kode_barang, b.nama_barang, d.harga, d.qty,
            (d.harga * d.qty) AS subtotal
     FROM transaksi_detail d
     INNER JOIN barang b ON d.barang_id = b.id
     WHERE d.transaksi_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$detail = mysqli_stmt_get_result($stmt);

//Dropdown Barang (Disabled jika sudah dipilih)
$stmt = mysqli_prepare($koneksi,
    "SELECT b.id, b.kode_barang, b.nama_barang, b.harga,
            (d.barang_id IS NOT NULL) AS terpakai
     FROM barang b
     LEFT JOIN transaksi_detail d
            ON d.barang_id = b.id AND d.transaksi_id = ?
     ORDER BY b.nama_barang");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$daftar_barang = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Transaksi #<?= e($master['id']) ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <p><a href="transaksi.php">&laquo; Kembali ke daftar transaksi</a></p>

    <h1>Detail Transaksi #<?= e($master['id']) ?></h1>
    <table>
        <tr><th>ID Transaksi</th><td><?= e($master['id']) ?></td></tr>
        <tr><th>Tanggal</th><td><?= e($master['waktu_transaksi']) ?></td></tr>
        <tr><th>Pelanggan</th><td><?= e($master['nama_pelanggan']) ?></td></tr>
        <tr><th>Keterangan</th><td><?= e($master['keterangan']) ?></td></tr>
        <tr><th>Total</th>
            <td><b>Rp <?= number_format($master['total'], 0, ',', '.') ?></b></td></tr>
    </table>

    <?php if ($pesan != "") { ?>
        <p class="<?= isset($_GET['ok']) ? 'sukses' : 'gagal' ?>"><?= e($pesan) ?></p>
    <?php } ?>

    <h2>Item Transaksi</h2>
    <table>
        <tr>
            <th>No</th><th>Kode</th><th>Nama Barang</th>
            <th>Harga</th><th>Qty</th><th>Subtotal</th>
        </tr>
        <?php
        $no = 1;
        while ($d = mysqli_fetch_assoc($detail)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($d['kode_barang']) ?></td>
            <td><?= e($d['nama_barang']) ?></td>
            <td class="kanan"><?= number_format($d['harga'], 0, ',', '.') ?></td>
            <td class="kanan"><?= e($d['qty']) ?></td>
            <td class="kanan"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php } ?>
    </table>

    <h2>Tambah Item</h2>
    <form method="post">
        <label>Barang</label>
        <select name="barang_id" required>
            <option value="">-- Pilih Barang --</option>
            <?php while ($b = mysqli_fetch_assoc($daftar_barang)) { ?>
                <option value="<?= e($b['id']) ?>" <?= $b['terpakai'] ? 'disabled' : '' ?>>
                    <?= e($b['kode_barang']) ?> - <?= e($b['nama_barang']) ?>
                    (Rp <?= number_format($b['harga'], 0, ',', '.') ?>)
                    <?= $b['terpakai'] ? '[sudah dipilih]' : '' ?>
                </option>
            <?php } ?>
        </select>

        <label>Qty</label>
        <input type="number" name="qty" min="1" value="1" required>

        <button type="submit" name="tambah">Tambah</button>
    </form>
</div>
</body>
</html>