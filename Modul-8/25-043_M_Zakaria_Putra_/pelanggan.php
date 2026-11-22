<?php
include 'includes/cek_login.php';
include 'includes/cek_owner.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'tampil';
$pesan = "";
$tipe  = "";

$data = ['id' => '', 'nama' => '', 'jenis_kelamin' => 'L', 'telp' => '', 'alamat' => ''];

// CREATE (id pelanggan diisi manual, contoh PLG006)
if (isset($_POST['simpan'])) {
    $id     = trim($_POST['id']);
    $nama   = trim($_POST['nama']);
    $jk     = $_POST['jenis_kelamin'] == 'P' ? 'P' : 'L';
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $id, $nama, $jk, $telp, $alamat);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: pelanggan.php?ok=tambah");
        exit;
    }
    $pesan = (mysqli_stmt_errno($stmt) == 1062) ? "ID pelanggan sudah digunakan." : "Data gagal disimpan.";
    $tipe  = "gagal";
    $aksi  = "tambah";
    $data  = array_merge($data, $_POST);
}

// UPDATE
if (isset($_POST['update'])) {
    $id     = $_POST['id'];
    $nama   = trim($_POST['nama']);
    $jk     = $_POST['jenis_kelamin'] == 'P' ? 'P' : 'L';
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = mysqli_prepare($koneksi,
        "UPDATE pelanggan SET nama = ?, jenis_kelamin = ?, telp = ?, alamat = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssss", $nama, $jk, $telp, $alamat, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: pelanggan.php?ok=ubah");
        exit;
    }
    $pesan = "Data gagal diubah.";
    $tipe  = "gagal";
    $aksi  = "edit";
    $data  = array_merge($data, $_POST);
}

// DELETE (dicek dulu apakah pelanggan sudah punya transaksi)
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];

    $cek = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jumlah FROM transaksi WHERE pelanggan_id = ?");
    mysqli_stmt_bind_param($cek, "s", $id);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

    if ($hasil['jumlah'] > 0) {
        $pesan = "Pelanggan tidak dapat dihapus karena sudah memiliki "
               . $hasil['jumlah'] . " transaksi.";
        $tipe = "gagal";
    } else {
        $del = mysqli_prepare($koneksi, "DELETE FROM pelanggan WHERE id = ?");
        mysqli_stmt_bind_param($del, "s", $id);
        mysqli_stmt_execute($del);
        $pesan = "Pelanggan berhasil dihapus.";
        $tipe = "sukses";
    }
}

// ambil data lama untuk form edit
if ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] != 'POST') {
    $id = $_GET['id'] ?? '';
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM pelanggan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$lama) {
        header("Location: pelanggan.php");
        exit;
    }
    $data = $lama;
}

if (isset($_GET['ok']) && $pesan == "") {
    $pesan = pesanSukses($_GET['ok']);
    $tipe  = "sukses";
}

$pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pelanggan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <h1>Data Pelanggan</h1>

    <?php if ($pesan != "") { ?>
        <p class="<?= $tipe ?>"><?= e($pesan) ?></p>
    <?php } ?>

    <?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>
        <h2><?= $aksi == 'edit' ? 'Edit' : 'Tambah' ?> Pelanggan</h2>
        <form method="post" class="form-data">
            <label>ID Pelanggan</label>
            <input type="text" name="id" maxlength="20" value="<?= e($data['id']) ?>"
                   <?= $aksi == 'edit' ? 'readonly' : 'required placeholder="contoh: PLG011"' ?>>

            <label>Nama</label>
            <input type="text" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required>

            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin">
                <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>

            <label>Telepon</label>
            <input type="text" name="telp" maxlength="12" value="<?= e($data['telp']) ?>">

            <label>Alamat</label>
            <textarea name="alamat" rows="3"><?= e($data['alamat']) ?></textarea>

            <p>
                <button type="submit" name="<?= $aksi == 'edit' ? 'update' : 'simpan' ?>">Simpan</button>
                <a href="pelanggan.php">Batal</a>
            </p>
        </form>
    <?php } else { ?>
        <p><a class="tombol" href="pelanggan.php?aksi=tambah">+ Tambah Pelanggan</a></p>

        <table>
            <tr><th>ID</th><th>Nama</th><th>JK</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
            <tr>
                <td><?= e($p['id']) ?></td>
                <td><?= e($p['nama']) ?></td>
                <td><?= e($p['jenis_kelamin']) ?></td>
                <td><?= e($p['telp']) ?></td>
                <td><?= e($p['alamat']) ?></td>
                <td>
                    <a href="pelanggan.php?aksi=edit&id=<?= urlencode($p['id']) ?>">Edit</a>
                    <form method="post" class="form-aksi" onsubmit="return confirm('Yakin hapus pelanggan ini?')">
                        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
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
