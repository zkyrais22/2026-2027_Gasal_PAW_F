INSERT INTO supplier (nama, telp, alamat) VALUES
('PT. Maju Bersama',   '08123456',   'Surabaya'),
('PT. Senang Sekali',  '081515563',  'Bangkalan'),
('PT. Segar Segar',    '0845454663', 'Surabaya'),
('CV. Sumber Rejeki',  '081234567890','Sidoarjo'),
('PT. Jaya Abadi',     '082111222333','Gresik'),
('CV. Berkah Makmur',  '085233344455','Malang'),
('PT. Mitra Sejati',   '087755566677','Mojokerto'),
('CV. Cahaya Utama',   '081399988877','Pasuruan'),
('PT. Tani Makmur',    '089612345678','Lamongan'),
('CV. Sentosa Jaya',   '081200011122','Surabaya'),
('PT. Uji Hapus',      '081000000000','Madura');
 
INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES
('BRG001','Beras 5kg',        65000, 50, 1),
('BRG002','Minyak Goreng 2L', 38000, 40, 2),
('BRG003','Gula Pasir 1kg',   16000, 80, 3),
('BRG004','Telur Ayam 1kg',   28000, 60, 4),
('BRG005','Tepung Terigu 1kg',12000, 70, 5),
('BRG006','Kopi Bubuk 200g',  22000, 45, 6),
('BRG007','Teh Celup 25s',     9000, 90, 7),
('BRG008','Susu UHT 1L',      18000, 55, 8),
('BRG009','Mie Instan',        3500,200, 9),
('BRG010','Sabun Mandi',       4500,120,10);
 
INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES
('PLG001','Budi Santoso',  'L','081211110001','Surabaya'),
('PLG002','Siti Aminah',   'P','081211110002','Sidoarjo'),
('PLG003','Andi Wijaya',   'L','081211110003','Gresik'),
('PLG004','Dewi Lestari',  'P','081211110004','Malang'),
('PLG005','Rudi Hartono',  'L','081211110005','Surabaya'),
('PLG006','Rina Marlina',  'P','081211110006','Bangkalan'),
('PLG007','Agus Salim',    'L','081211110007','Mojokerto'),
('PLG008','Putri Ayu',     'P','081211110008','Pasuruan'),
('PLG009','Eko Prasetyo',  'L','081211110009','Lamongan'),
('PLG010','Nina Kartika',  'P','081211110010','Surabaya');
 
INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id) VALUES
('2026-10-01','Pembelian bulanan',      97000,'PLG001'),
('2026-10-03','Belanja harian',         62000,'PLG002'),
('2026-10-05','Beli telur',             28000,'PLG003'),
('2026-10-07','Belanja kopi dan teh',   53000,'PLG004'),
('2026-10-09','Beli susu',              54000,'PLG005'),
('2026-10-12','Belanja kebutuhan dapur',44000,'PLG006'),
('2026-10-15','Beli beras banyak',     130000,'PLG007'),
('2026-10-18','Belanja gula dan tepung',92000,'PLG008'),
('2026-10-22','Belanja minyak dan telur',104000,'PLG009'),
('2026-10-26','Belanja teh dan susu',   72000,'PLG010');
 
INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES
(1,1,65000,1),(1,3,16000,2),
(2,2,38000,1),(2,5,12000,2),
(3,4,28000,1),
(4,6,22000,2),(4,7,9000,1),
(5,8,18000,3),
(6,9,3500,10),(6,10,4500,2),
(7,1,65000,2),
(8,3,16000,5),(8,5,12000,1),
(9,2,38000,2),(9,4,28000,1),
(10,7,9000,4),(10,8,18000,2);
 
INSERT INTO pembayaran (waktu_bayar, total, metode, transaksi_id) VALUES
('2026-10-01 10:15:00', 97000,'TUNAI',   1),
('2026-10-03 11:20:00', 62000,'TRANSFER',2),
('2026-10-05 09:05:00', 28000,'TUNAI',   3),
('2026-10-07 14:30:00', 53000,'EDC',     4),
('2026-10-09 16:45:00', 54000,'TRANSFER',5),
('2026-10-12 08:50:00', 44000,'TUNAI',   6),
('2026-10-15 13:10:00',130000,'EDC',     7),
('2026-10-18 15:25:00', 92000,'TRANSFER',8),
('2026-10-22 10:40:00',104000,'TUNAI',   9),
('2026-10-26 17:00:00', 72000,'EDC',    10);
 
INSERT INTO user (username, password, nama, alamat, hp, level) VALUES
('admin',  MD5('admin123'),'Administrator','Surabaya', '081300000001',1),
('kasir1', MD5('kasir123'),'Kasir Satu',   'Surabaya', '081300000002',2),
('kasir2', MD5('kasir123'),'Kasir Dua',    'Sidoarjo', '081300000003',2),
('kasir3', MD5('kasir123'),'Kasir Tiga',   'Gresik',   '081300000004',2),
('gudang1',MD5('gudang123'),'Staf Gudang 1','Malang',  '081300000005',3),
('gudang2',MD5('gudang123'),'Staf Gudang 2','Mojokerto','081300000006',3),
('owner',  MD5('owner123'),'Pemilik Toko', 'Surabaya', '081300000007',1),
('sales1', MD5('sales123'),'Sales Satu',   'Pasuruan', '081300000008',2),
('sales2', MD5('sales123'),'Sales Dua',    'Lamongan', '081300000009',2),
('guest',  MD5('guest123'),'Tamu',         'Bangkalan','081300000010',4);