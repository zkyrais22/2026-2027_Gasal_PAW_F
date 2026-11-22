<?php
include 'includes/cek_login.php';
include 'includes/cek_owner.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'tampil';
$pesan = "";
$tipe  = "";

$data = ['id' => '', 'nama' => '', 'telp' => '', 'alamat' => ''];

// CREATE
if (isset($_POST['simpan'])) {
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nama, $telp, $alamat);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: supplier.php?ok=tambah");
        exit;
    }
    $pesan = "Data gagal disimpan.";
    $tipe  = "gagal";
    $aksi  = "tambah";
    $data  = array_merge($data, $_POST);
}

// UPDATE
if (isset($_POST['update'])) {
    $id     = (int) $_POST['id'];
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = mysqli_prepare($koneksi, "UPDATE supplier SET nama = ?, telp = ?, alamat = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $telp, $alamat, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: supplier.php?ok=ubah");
        exit;
    }
    $pesan = "Data gagal diubah.";
    $tipe  = "gagal";
    $aksi  = "edit";
    $data  = array_merge($data, $_POST);
}

// DELETE (dicek dulu apakah supplier masih dipakai barang)
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    $cek = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jumlah FROM barang WHERE supplier_id = ?");
    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

    if ($hasil['jumlah'] > 0) {
        $pesan = "Supplier tidak dapat dihapus karena masih digunakan pada "
               . $hasil['jumlah'] . " barang.";
        $tipe = "gagal";
    } else {
        $del = mysqli_prepare($koneksi, "DELETE FROM supplier WHERE id = ?");
        mysqli_stmt_bind_param($del, "i", $id);
        mysqli_stmt_execute($del);
        $pesan = "Supplier berhasil dihapus.";
        $tipe = "sukses";
    }
}

// ambil data lama untuk form edit
if ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] != 'POST') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM supplier WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$lama) {
        header("Location: supplier.php");
        exit;
    }
    $data = $lama;
}

if (isset($_GET['ok']) && $pesan == "") {
    $pesan = pesanSukses($_GET['ok']);
    $tipe  = "sukses";
}

$supplier = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Supplier</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <h1>Data Supplier</h1>

    <?php if ($pesan != "") { ?>
        <p class="<?= $tipe ?>"><?= e($pesan) ?></p>
    <?php } ?>

    <?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>
        <h2><?= $aksi == 'edit' ? 'Edit' : 'Tambah' ?> Supplier</h2>
        <form method="post" class="form-data">
            <input type="hidden" name="id" value="<?= e($data['id']) ?>">

            <label>Nama Supplier</label>
            <input type="text" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required>

            <label>Telepon</label>
            <input type="text" name="telp" maxlength="12" value="<?= e($data['telp']) ?>">

            <label>Alamat</label>
            <textarea name="alamat" rows="3"><?= e($data['alamat']) ?></textarea>

            <p>
                <button type="submit" name="<?= $aksi == 'edit' ? 'update' : 'simpan' ?>">Simpan</button>
                <a href="supplier.php">Batal</a>
            </p>
        </form>
    <?php } else { ?>
        <p><a class="tombol" href="supplier.php?aksi=tambah">+ Tambah Supplier</a></p>

        <table>
            <tr><th>ID</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
            <?php while ($s = mysqli_fetch_assoc($supplier)) { ?>
            <tr>
                <td><?= e($s['id']) ?></td>
                <td><?= e($s['nama']) ?></td>
                <td><?= e($s['telp']) ?></td>
                <td><?= e($s['alamat']) ?></td>
                <td>
                    <a href="supplier.php?aksi=edit&id=<?= e($s['id']) ?>">Edit</a>
                    <form method="post" class="form-aksi" onsubmit="return confirm('Yakin hapus supplier ini?')">
                        <input type="hidden" name="id" value="<?= e($s['id']) ?>">
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
