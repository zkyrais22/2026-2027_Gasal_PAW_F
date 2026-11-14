<?php
require 'koneksi.php';

$tanggal_mulai   = $_GET['tanggal_mulai']   ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

$error = '';
$tampil = false;
$labels = []; $data_total = []; $rekap = [];
$total_pendapatan = 0; $total_pelanggan = 0;

if ($tanggal_mulai !== '' && $tanggal_selesai !== '') {
    if (!tanggalValid($tanggal_mulai) || !tanggalValid($tanggal_selesai)) {
        $error = 'Format tanggal tidak valid.';
    } elseif ($tanggal_mulai > $tanggal_selesai) {
        $error = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.';
    } else {
        $tampil = true;
        $lap = ambilLaporan($conn, $tanggal_mulai, $tanggal_selesai);
        $rekap            = $lap['rekap'];
        $total_pelanggan  = $lap['pelanggan'];
        $total_pendapatan = $lap['total'];
        foreach ($rekap as $r) {
            $labels[]     = $r['tgl'];
            $data_total[] = (int)$r['total_harian'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: black; }
        h1 small { font-size: 14px; font-weight: normal; color: gray; }
        form { margin-bottom: 12px; }
        input, button { padding: 6px 10px; margin-right: 6px; }
        .btn { background: darkorange; color: white; border: 0; border-radius: 4px; cursor: pointer; padding: 7px 14px; text-decoration: none; display: inline-block; font-size: 13.3px; }
        .btn-primary { background: navy; }
        .error { color: firebrick; background: mistyrose; padding: 8px 12px; border-radius: 4px; margin: 10px 0; }
        .info  { color: dimgray; background: whitesmoke; padding: 8px 12px; border-radius: 4px; margin: 10px 0; }
        .chart-box { position: relative; width: 100%; max-width: 900px; height: 350px; margin: 16px 0; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; margin-top: 10px; }
        th, td { border: 1px solid lightgray; padding: 8px; text-align: left; }
        th { background: lightblue; }
        .card { display: inline-block; margin-top: 16px; min-width: 420px; border: 1px solid lightgray; }
        .card .head { display: flex; background: lightblue; font-weight: bold; }
        .card .body { display: flex; }
        .card .head div, .card .body div { flex: 1; padding: 8px 12px; }
        .card .body div { text-align: right; font-weight: bold; }

 
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            th, .card .head { background: lightblue !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<h1>Rekap Laporan Penjualan
    <?php if ($tampil): ?>
        <small><?= htmlspecialchars($tanggal_mulai) ?> sampai <?= htmlspecialchars($tanggal_selesai) ?></small>
    <?php endif; ?>
</h1>

<!-- FORM FILTER + TOMBOL -->
<div class="no-print">
    <form method="GET" action="">
        Tanggal Mulai :
        <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($tanggal_mulai) ?>" required>
        Tanggal Selesai :
        <input type="date" name="tanggal_selesai" value="<?= htmlspecialchars($tanggal_selesai) ?>" required>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    <?php if ($tampil && !empty($rekap)): ?>
        <button class="btn" onclick="window.print()">Cetak</button>
        <a class="btn" href="excel.php?tanggal_mulai=<?= urlencode($tanggal_mulai) ?>&tanggal_selesai=<?= urlencode($tanggal_selesai) ?>">Excel</a>
    <?php endif; ?>
</div>

<?php if ($error): ?>
    <div class="error no-print"><?= htmlspecialchars($error) ?></div>
<?php elseif (!$tampil): ?>
    <div class="info no-print">Pilih rentang tanggal lalu tekan "Tampilkan".</div>
<?php elseif (empty($rekap)): ?>
    <div class="info">Tidak ada transaksi pada rentang tanggal tersebut.</div>
<?php else: ?>

    <!-- GRAFIK -->
    <div class="chart-box"><canvas id="myChart"></canvas></div>

    <!-- TABEL REKAP-->
    <table>
        <tr><th>No</th><th>Total Pendapatan</th><th>Tanggal Transaksi</th></tr>
        <?php $no = 1; foreach ($rekap as $row): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>Rp. <?= number_format($row['total_harian'], 0, ',', '.') ?></td>
            <td><?= date('d M Y', strtotime($row['tgl'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- CARD RINGKASAN TOTAL KUMULATIF -->
    <div class="card">
        <div class="head"><div>Jumlah Pelanggan</div><div>Jumlah Pendapatan</div></div>
        <div class="body">
            <div><?= $total_pelanggan ?> Orang</div>
            <div>Rp. <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('myChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Pendapatan',
                    data: <?= json_encode($data_total) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, 
                scales: {
                    x: { title: { display: true, text: 'Tanggal Transaksi' } },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total Pendapatan (Rp)' },
                        ticks: { callback: v => 'Rp. ' + v.toLocaleString('id-ID') }
                    }
                }
            }
        });
    </script>
<?php endif; ?>
</body>
</html>