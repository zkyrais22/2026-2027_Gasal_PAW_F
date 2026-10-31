<?php
include 'koneksi.php';

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data lama berdasarkan ID
data = mysqli_query($koneksi, "SELECT * FROM supplier WHERE id = '$id'");
$d = mysqli_fetch_assoc($data);

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];

    $update = mysqli_query($koneksi, "UPDATE supplier SET nama='$nama', telp='$telp', alamat='$alamat' WHERE id='$id'");

    if ($update) {
        echo "<script>alert('Data berhasil di-update!'); window.location='Supplier.php';</script>";
    } else {
        echo "<script>alert('Gagal meng-update data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Supplier</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        .form-container { width: 500px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; height: 80px; }
        button { background: #ffc107; color: black; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #e0a800; }
        .btn-kembali { background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit Data Master Supplier</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label>Nama:</label>
                <input type="text" name="nama" value="<?= $d['nama']; ?>" required>
            </div>
            <div class="form-group">
                <label>Telp:</label>
                <input type="text" name="telp" value="<?= $d['telp']; ?>" required>
            </div>
            <div class="form-group">
                <label>Alamat:</label>
                <textarea name="alamat" required><?= $d['alamat']; ?></textarea>
            </div>
            <button type="submit" name="update">Update</button>
            <a href="Supplier.php" class="btn-kembali">Batal</a>
        </form>
    </div>

</body>
</html>