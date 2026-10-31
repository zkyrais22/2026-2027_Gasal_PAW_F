<?php
require 'koneksi.php';

$result = $conn->query("SELECT * FROM supplier ORDER BY id ASC");

$pesan  = $_GET['pesan'] ?? '';
$error  = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Data Master Supplier</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap" style="margin: 30px;">
  <div class="head" style="margin-bottom: 20px;">
    <h3 class="judul">Data Master Supplier</h3>
    <a href="supplier_tambah.php" class="btn btn-sm btn-success">Tambah Data</a>
  </div>

  <?php if ($pesan)  { ?><div class="alert alert-success"><?= htmlspecialchars($pesan) ?></div><?php } ?>
  <?php if ($error)  { ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php } ?>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Telp</th>
        <th>Alamat</th>
        <th>Tindakan</th>
      </tr>
    </thead>
    <tbody>
    <?php 
    $no = 1; 
    while ($row = mysqli_fetch_assoc($result)) { 
    ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['telp']) ?></td>
        <td><?= htmlspecialchars($row['alamat']) ?></td>
        <td>
          <a href="supplier_edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
          <a href="supplier_hapus.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger"
             onclick="return confirm('Anda yakin akan menghapus supplier ini?')">Hapus</a>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>