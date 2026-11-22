<?php
include 'includes/cek_login.php';
include 'includes/cek_owner.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'tampil';
$pesan = "";
$tipe  = "";

$data = ['id_user' => '', 'username' => '', 'nama' => '', 'alamat' => '', 'hp' => '', 'level' => 2];

// CREATE
if (isset($_POST['simpan'])) {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']);
    $nama     = trim($_POST['nama']);
    $alamat   = trim($_POST['alamat']);
    $hp       = trim($_POST['hp']);
    $level    = (int) $_POST['level'];

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO user (username, password, nama, alamat, hp, level) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssi", $username, $password, $nama, $alamat, $hp, $level);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: user.php?ok=tambah");
        exit;
    }
    $pesan = (mysqli_stmt_errno($stmt) == 1062) ? "Username sudah digunakan." : "Data gagal disimpan.";
    $tipe  = "gagal";
    $aksi  = "tambah";
    $data  = array_merge($data, $_POST);
}

// UPDATE (password dikosongkan = tidak diganti)
if (isset($_POST['update'])) {
    $id       = (int) $_POST['id_user'];
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama']);
    $alamat   = trim($_POST['alamat']);
    $hp       = trim($_POST['hp']);
    $level    = (int) $_POST['level'];

    if ($_POST['password'] != "") {
        $password = md5($_POST['password']);
        $stmt = mysqli_prepare($koneksi,
            "UPDATE user SET username = ?, password = ?, nama = ?, alamat = ?, hp = ?, level = ?
             WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "sssssii", $username, $password, $nama, $alamat, $hp, $level, $id);
    } else {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE user SET username = ?, nama = ?, alamat = ?, hp = ?, level = ?
             WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "ssssii", $username, $nama, $alamat, $hp, $level, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        // kalau yang diedit akun sendiri, session ikut diperbarui
        if ($id == $_SESSION['id_user']) {
            $_SESSION['nama']  = $nama;
            $_SESSION['level'] = $level;
        }
        header("Location: user.php?ok=ubah");
        exit;
    }
    $pesan = (mysqli_stmt_errno($stmt) == 1062) ? "Username sudah digunakan." : "Data gagal diubah.";
    $tipe  = "gagal";
    $aksi  = "edit";
    $data  = array_merge($data, $_POST);
}

// DELETE
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id_user'];

    if ($id == $_SESSION['id_user']) {
        $pesan = "Akun yang sedang dipakai login tidak dapat dihapus.";
        $tipe  = "gagal";
    } else {
        $del = mysqli_prepare($koneksi, "DELETE FROM user WHERE id_user = ?");
        mysqli_stmt_bind_param($del, "i", $id);
        mysqli_stmt_execute($del);
        $pesan = "User berhasil dihapus.";
        $tipe  = "sukses";
    }
}

// ambil data lama untuk form edit
if ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] != 'POST') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($koneksi,
        "SELECT id_user, username, nama, alamat, hp, level FROM user WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$lama) {
        header("Location: user.php");
        exit;
    }
    $data = $lama;
}

if (isset($_GET['ok']) && $pesan == "") {
    $pesan = pesanSukses($_GET['ok']);
    $tipe  = "sukses";
}

$daftar_user = mysqli_query($koneksi,
    "SELECT id_user, username, nama, alamat, hp, level FROM user ORDER BY id_user");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="konten">
    <h1>Data User</h1>

    <?php if ($pesan != "") { ?>
        <p class="<?= $tipe ?>"><?= e($pesan) ?></p>
    <?php } ?>

    <?php if ($aksi == 'tambah' || $aksi == 'edit') { ?>
        <h2><?= $aksi == 'edit' ? 'Edit' : 'Tambah' ?> User</h2>
        <form method="post" class="form-data">
            <input type="hidden" name="id_user" value="<?= e($data['id_user']) ?>">

            <label>Username</label>
            <input type="text" name="username" maxlength="30" value="<?= e($data['username']) ?>" required>

            <label>Password <?= $aksi == 'edit' ? '(kosongkan jika tidak diganti)' : '' ?></label>
            <input type="password" name="password" maxlength="30" <?= $aksi == 'edit' ? '' : 'required' ?>>

            <label>Nama</label>
            <input type="text" name="nama" maxlength="50" value="<?= e($data['nama']) ?>" required>

            <label>Alamat</label>
            <input type="text" name="alamat" maxlength="150" value="<?= e($data['alamat']) ?>">

            <label>No. HP</label>
            <input type="text" name="hp" maxlength="20" value="<?= e($data['hp']) ?>">

            <label>Level</label>
            <select name="level">
                <option value="1" <?= $data['level'] == 1 ? 'selected' : '' ?>>1 - Owner</option>
                <option value="2" <?= $data['level'] == 2 ? 'selected' : '' ?>>2 - Kasir</option>
            </select>

            <p>
                <button type="submit" name="<?= $aksi == 'edit' ? 'update' : 'simpan' ?>">Simpan</button>
                <a href="user.php">Batal</a>
            </p>
        </form>
    <?php } else { ?>
        <p><a class="tombol" href="user.php?aksi=tambah">+ Tambah User</a></p>

        <table>
            <tr><th>ID</th><th>Username</th><th>Nama</th><th>Alamat</th><th>No. HP</th><th>Level</th><th>Aksi</th></tr>
            <?php while ($u = mysqli_fetch_assoc($daftar_user)) { ?>
            <tr>
                <td><?= e($u['id_user']) ?></td>
                <td><?= e($u['username']) ?></td>
                <td><?= e($u['nama']) ?></td>
                <td><?= e($u['alamat']) ?></td>
                <td><?= e($u['hp']) ?></td>
                <td><?= $u['level'] == 1 ? 'Owner' : 'Kasir' ?></td>
                <td>
                    <a href="user.php?aksi=edit&id=<?= e($u['id_user']) ?>">Edit</a>
                    <form method="post" class="form-aksi" onsubmit="return confirm('Yakin hapus user ini?')">
                        <input type="hidden" name="id_user" value="<?= e($u['id_user']) ?>">
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
