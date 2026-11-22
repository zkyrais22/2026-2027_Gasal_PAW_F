<?php
include 'includes/cek_login.php';
include 'includes/cek_owner.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'tampil';
$pesan = "";
$tipe  = "";

// isi awal form
$data = ['id' => '', 'kode_barang' => '', 'nama_barang' => '',
         'harga' => '', 'stok' => '', 'supplier_id' => ''];

// CREATE
if (isset($_POST['simpan'])) {
    $kode     = trim($_POST['kode_barang']);
    $nama     = trim($_POST['nama_barang']);
    $harga    = (int) $_POST['harga'];
    $stok     = (int) $_POST['stok'];
    $supplier = (int) $_POST['supplier_id'];

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id)
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiii", $kode, $nama, $harga, $stok, $supplier);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: barang.php?ok=tambah");
        exit;
    }
    $pesan = (mysqli_stmt_errno($stmt) == 1062) ? "Kode barang sudah digunakan." : "Data gagal disimpan.";
    $tipe  = "gagal";
    $aksi  = "tambah";
    $data  = array_merge($data, $_POST);
}

// UPDATE
if (isset($_POST['update'])) {
    $id       = (int) $_POST['id'];
    $kode     = trim($_POST['kode_barang']);
    $nama     = trim($_POST['nama_barang']);
    $harga    = (int) $_POST['harga'];
    $stok     = (int) $_POST['stok'];
    $supplier = (int) $_POST['supplier_id'];

    $stmt = mysqli_prepare($koneksi,
        "UPDATE barang SET kode_barang = ?, nama_barang = ?, harga = ?, stok = ?, supplier_id = ?
         WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssiiii", $kode, $nama, $harga, $stok, $supplier, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: barang.php?ok=ubah");
        exit;
    }
    $pesan = (mysqli_stmt_errno($stmt) == 1062) ? "Kode barang sudah digunakan." : "Data gagal diubah.";
    $tipe  = "gagal";
    $aksi  = "edit";
    $data  = array_merge($data, $_POST);
}

// DELETE
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    $cek = mysqli_prepare($koneksi,
        "SELECT COUNT(*) AS jumlah FROM transaksi_detail WHERE barang_id = ?");
    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

    if ($hasil['jumlah'] > 0) {
        $pesan = "Barang tidak dapat dihapus karena masih digunakan pada "
               . $hasil['jumlah'] . " transaksi.";
        $tipe = "gagal";
    } else {
        $del = mysqli_prepare($koneksi, "DELETE FROM barang WHERE id = ?");
        mysqli_stmt_bind_param($del, "i", $id);
        mysqli_stmt_execute($del);
        $pesan = "Barang berhasil dihapus.";
        $tipe = "sukses";
    }
}

// ambil data lama untuk form edit
if ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] != 'POST') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$lama) {
        header("Location: barang.php");
        exit;
    }
    $data = $lama;
}

// pesan sukses setelah redirect
if (isset($_GET['ok']) && $pesan == "") {
    $pesan = pesanSukses($_GET['ok']);
    $tipe  = "sukses";
}

$daftar_supplier = mysqli_query($koneksi, "SELECT id, nama FROM supplier ORDER BY nama");

$barang = mysqli_query($koneksi,
    "SELECT b.id, b.kode_barang, b.nama_barang, b.harga, b.stok, s.nama AS supplier
     FROM barang b
     LEFT JOIN supplier s ON b.supplier_id = s.id
     ORDER BY b.id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <h1>Data Barang</h1>

    <?php if ($pesan != "") { ?>
        <p class="<?= $tipe ?>"><?= e($pesan) ?></p>
    <?php } ?>

    <?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>
        <h2><?= $aksi == 'edit' ? 'Edit' : 'Tambah' ?> Barang</h2>
        <form method="post" class="form-data">
            <input type="hidden" name="id" value="<?= e($data['id']) ?>">

            <label>Kode Barang</label>
            <input type="text" name="kode_barang" maxlength="10" value="<?= e($data['kode_barang']) ?>" required>

            <label>Nama Barang</label>
            <input type="text" name="nama_barang" maxlength="100" value="<?= e($data['nama_barang']) ?>" required>

            <label>Harga</label>
            <input type="number" name="harga" min="0" value="<?= e($data['harga']) ?>" required>

            <label>Stok</label>
            <input type="number" name="stok" min="0" value="<?= e($data['stok']) ?>" required>

            <label>Supplier</label>
            <select name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php while ($s = mysqli_fetch_assoc($daftar_supplier)) { ?>
                    <option value="<?= e($s['id']) ?>" <?= $s['id'] == $data['supplier_id'] ? 'selected' : '' ?>>
                        <?= e($s['nama']) ?>
                    </option>
                <?php } ?>
            </select>

            <p>
                <button type="submit" name="<?= $aksi == 'edit' ? 'update' : 'simpan' ?>">Simpan</button>
                <a href="barang.php">Batal</a>
            </p>
        </form>
    <?php } else { ?>
        <p><a class="tombol" href="barang.php?aksi=tambah">+ Tambah Barang</a></p>

        <table>
            <tr>
                <th>ID</th><th>Kode</th><th>Nama Barang</th>
                <th>Harga</th><th>Stok</th><th>Supplier</th><th>Aksi</th>
            </tr>
            <?php while ($b = mysqli_fetch_assoc($barang)) { ?>
            <tr>
                <td><?= e($b['id']) ?></td>
                <td><?= e($b['kode_barang']) ?></td>
                <td><?= e($b['nama_barang']) ?></td>
                <td class="kanan"><?= number_format($b['harga'], 0, ',', '.') ?></td>
                <td class="kanan"><?= e($b['stok']) ?></td>
                <td><?= e($b['supplier']) ?></td>
                <td>
                    <a href="barang.php?aksi=edit&id=<?= e($b['id']) ?>">Edit</a>
                    <form method="post" class="form-aksi" onsubmit="return confirm('Yakin hapus barang ini?')">
                        <input type="hidden" name="id" value="<?= e($b['id']) ?>">
                        <button type="submit" name="hapus">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>
</body>
</html>
