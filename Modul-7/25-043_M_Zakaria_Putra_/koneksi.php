<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'penjualan2'; 

$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');


// validasi tanggals
function tanggalValid($tgl) {
    $d = DateTime::createFromFormat('Y-m-d', $tgl);
    return $d && $d->format('Y-m-d') === $tgl;
}

// Ambil Data Laporan cuyy
function ambilLaporan($conn, $mulai, $selesai) {
    $rekap = [];

    // Agregasi pendapatan per hari
    $sql = "SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian
            FROM transaksi
            WHERE DATE(waktu_transaksi) BETWEEN ? AND ?
            GROUP BY DATE(waktu_transaksi)
            ORDER BY tgl ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $mulai, $selesai);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $total_pendapatan = 0;
    while ($r = mysqli_fetch_assoc($res)) {
        $rekap[] = $r;
        $total_pendapatan += $r['total_harian'];
    }
    mysqli_stmt_close($stmt);

    // Jumlah pelanggan unik
    $sql2 = "SELECT COUNT(DISTINCT pelanggan_id) AS jml
             FROM transaksi
             WHERE DATE(waktu_transaksi) BETWEEN ? AND ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, 'ss', $mulai, $selesai);
    mysqli_stmt_execute($stmt2);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
    mysqli_stmt_close($stmt2);

    return [
        'rekap'     => $rekap,
        'pelanggan' => (int)($row['jml'] ?? 0),
        'total'     => $total_pendapatan,
    ];
}