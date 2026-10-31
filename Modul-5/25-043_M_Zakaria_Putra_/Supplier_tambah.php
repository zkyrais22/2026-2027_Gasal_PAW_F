<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    $stmt = $conn->prepare("INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $telp, $alamat);
    $stmt->execute();

    header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil ditambahkan"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah Data Master Supplier Baru</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
  <h2 class="form-judul">Tambah Data Master Supplier Baru</h2>

  <form method="post" class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-4 control-label">Nama</label>
      <div class="col-sm-5"><input type="text" name="nama" class="form-control" placeholder="Nama" maxlength="100" required></div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Telp</label>
      <div class="col-sm-3"><input type="text" name="telp" class="form-control" placeholder="telp" maxlength="12" required></div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Alamat</label>
      <div class="col-sm-3"><input type="text" name="alamat" class="form-control" placeholder="alamat" required></div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-4 col-sm-6">
        <button type="submit" class="btn btn-sm btn-hijau">Simpan</button>
        <a href="supplier.php" class="btn btn-sm btn-merah">Batal</a>
      </div>
    </div>
  </form>
</div>
</body>
</html>