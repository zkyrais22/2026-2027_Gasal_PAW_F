<?php
require 'koneksi.php';

// Proses simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) $_POST['id'];
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = $conn->prepare("UPDATE supplier SET nama=?, telp=?, alamat=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $telp, $alamat, $id);
    $stmt->execute();

    header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil diupdate"));
    exit;
}

// Ambil data lama untuk ditampilkan di form
$id   = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM supplier WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: supplier.php?error=" . urlencode("Data supplier tidak ditemukan"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Data Master Supplier</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
  <h2 class="form-judul">Edit Data Master Supplier</h2>

  <form method="post" class="form-horizontal">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">
    <div class="form-group">
      <label class="col-sm-4 control-label">Nama</label>
      <div class="col-sm-5"><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" maxlength="100" required></div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Telp</label>
      <div class="col-sm-3"><input type="text" name="telp" class="form-control" value="<?= htmlspecialchars($data['telp']) ?>" maxlength="12" required></div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Alamat</label>
      <div class="col-sm-3"><input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat']) ?>" required></div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-4 col-sm-6">
        <button type="submit" class="btn btn-sm btn-hijau">Update</button>
        <a href="supplier.php" class="btn btn-sm btn-merah">Batal</a>
      </div>
    </div>
  </form>
</div>
</body>
</html>