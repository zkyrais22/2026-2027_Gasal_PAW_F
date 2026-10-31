<?php 
require 'koneksi.php';

$id = (int) ($_GET['id']?? 0);

try{
	$stmt = $conn->prepare("DELETE FROM supplier WHERE id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil dihapus"));
}catch (mysql_sql_exception $e) {
	// Gagal karena supplier dipakai di tabel barang (foreign key)
	header("Location: supplier.php?error=" . urlencode("supplier tidak bisa dihapus karena masih dipakai di data barang"));
}
exit;
 ?>