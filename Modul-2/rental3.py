"""
Rental Kendaraan - Tugas OOP (10 class)

  Hierarki pewarisan (single inheritance, 3 level):
    1. Kendaraan        <- Abstract class / Level 1
    2. KendaraanRental  <- Level 2
    3. Mobil            <- Level 3 (override + polimorfisme)
    4. Motor            <- Level 3 (override + polimorfisme)

  Komponen pendukung (Enkapsulasi & Asosiasi):
    5. Pelanggan   6. Rental   7. Pembayaran   8. Admin   9. Nota

  Alur pemesanan online:
    Pelanggan memesan -> Rental berstatus MENUNGGU -> Admin menanggapi
    (setujui / tolak) -> AKTIF atau DITOLAK -> kendaraan kembali -> SELESAI

  Driver:
    10. Main

Hak akses (konvensi Python):
  public     -> nama     (bebas diakses)
  protected  -> _nama    (untuk class itu sendiri & turunannya)
  private    -> __nama   (name mangling, hanya di dalam class itu sendiri)
"""

import datetime
import json
import os
from abc import ABC, abstractmethod


def rupiah(angka):
    return "Rp " + f"{angka:,.0f}".replace(",", ".")


# ======================================================================
# 1. KENDARAAN  -  Abstract class / Level 1
# ======================================================================
class Kendaraan(ABC):
    """Pucuk hierarki. Menyimpan identitas kendaraan; aturan sewa diserahkan ke subclass."""

    def __init__(self, plat_nomor, merek, tahun):
        self.merek = merek                    # PUBLIC
        self.__plat_nomor = plat_nomor        # PRIVATE (identitas tidak boleh berubah)
        self._tahun = tahun                   # PROTECTED

    @property
    def plat_nomor(self):
        return self.__plat_nomor

    @property
    def tahun(self):
        return self._tahun

    @abstractmethod
    def jenis_kendaraan(self):
        """Nama jenis kendaraan."""

    @abstractmethod
    def hitung_biaya_sewa(self, lama_hari):
        """Total biaya sewa untuk lama_hari."""

    @abstractmethod
    def hitung_deposit(self):
        """Uang jaminan."""


# ======================================================================
# 2. KENDARAAN RENTAL  -  Level 2
# ======================================================================
class KendaraanRental(Kendaraan):
    """Menambah hal khusus rental: tarif, status ketersediaan, diskon, denda."""

    DENDA_FAKTOR = 1.5      # denda/hari = tarif x faktor (bisa di-override)

    def __init__(self, plat_nomor, merek, tahun, tarif_dasar):
        super().__init__(plat_nomor, merek, tahun)
        self._tarif_dasar = tarif_dasar       # PROTECTED
        self._status = "TERSEDIA"             # PROTECTED

    @property
    def tarif_dasar(self):
        return self._tarif_dasar

    @property
    def status(self):
        return self._status

    def tersedia(self):
        return self._status == "TERSEDIA"

    def tandai_disewa(self):
        self._status = "DISEWA"

    def tandai_tersedia(self):
        self._status = "TERSEDIA"

    def _diskon_durasi(self, lama_hari):          # PROTECTED method
        return 0.10 if lama_hari >= 7 else 0.0

    def hitung_biaya_sewa(self, lama_hari):
        return self._tarif_dasar * lama_hari * (1 - self._diskon_durasi(lama_hari))

    def hitung_deposit(self):
        return 300_000

    def hitung_denda(self, hari_terlambat):
        return hari_terlambat * self._tarif_dasar * self.DENDA_FAKTOR

    def validasi_pelanggan(self, pelanggan):
        """Hook: subclass menambah syarat. Return (boleh, alasan)."""
        return True, ""

    def ringkas(self):
        return (f"{self.plat_nomor:<11} {self.merek:<16} {self.jenis_kendaraan():<7} "
                f"{rupiah(self._tarif_dasar):>11}/hari  {self._status}")


# ======================================================================
# 3. MOBIL  -  Level 3 (override + polimorfisme)
# ======================================================================
class Mobil(KendaraanRental):
    """Mobil: SIM A, umur min 21, biaya cuci + asuransi, deposit & denda lebih besar."""

    BIAYA_CUCI = 50_000
    PERSEN_ASURANSI = 0.03
    DENDA_FAKTOR = 2.0                                  # OVERRIDE atribut

    def __init__(self, plat_nomor, merek, tahun, tarif_dasar, jumlah_pintu=4):
        super().__init__(plat_nomor, merek, tahun, tarif_dasar)
        self._jumlah_pintu = jumlah_pintu               # PROTECTED

    def jenis_kendaraan(self):                          # OVERRIDE (implementasi abstrak)
        return "Mobil"

    def hitung_biaya_sewa(self, lama_hari):             # OVERRIDE
        dasar = super().hitung_biaya_sewa(lama_hari)
        asuransi = self._tarif_dasar * lama_hari * self.PERSEN_ASURANSI
        return dasar + self.BIAYA_CUCI + asuransi

    def hitung_deposit(self):                           # OVERRIDE
        return 1_000_000

    def validasi_pelanggan(self, pelanggan):            # OVERRIDE
        if not pelanggan.punya_sim("A"):
            return False, "Mobil membutuhkan SIM A."
        if pelanggan.umur < 21:
            return False, "Umur minimal penyewa mobil adalah 21 tahun."
        return super().validasi_pelanggan(pelanggan)


# ======================================================================
# 4. MOTOR  -  Level 3 (override + polimorfisme)
# ======================================================================
class Motor(KendaraanRental):
    """Motor: SIM C, umur min 17, sewa helm per hari, diskon durasi lebih besar."""

    SEWA_HELM_PER_HARI = 5_000

    def __init__(self, plat_nomor, merek, tahun, tarif_dasar, cc=125):
        super().__init__(plat_nomor, merek, tahun, tarif_dasar)
        self._cc = cc                                   # PROTECTED

    def jenis_kendaraan(self):                          # OVERRIDE (implementasi abstrak)
        return "Motor"

    def _diskon_durasi(self, lama_hari):                # OVERRIDE method protected
        return 0.15 if lama_hari >= 7 else 0.0

    def hitung_biaya_sewa(self, lama_hari):             # OVERRIDE
        return super().hitung_biaya_sewa(lama_hari) + self.SEWA_HELM_PER_HARI * lama_hari

    def hitung_deposit(self):                           # OVERRIDE
        return 400_000

    def validasi_pelanggan(self, pelanggan):            # OVERRIDE
        if not pelanggan.punya_sim("C"):
            return False, "Motor membutuhkan SIM C."
        if pelanggan.umur < 17:
            return False, "Umur minimal penyewa motor adalah 17 tahun."
        return super().validasi_pelanggan(pelanggan)


# ======================================================================
# 5. PELANGGAN  -  komponen (Enkapsulasi)
# ======================================================================
class Pelanggan:
    """Data pribadi disembunyikan; hanya bisa dibaca lewat method/property."""

    def __init__(self, nama, nik, umur, jenis_sim):
        if umur <= 0:
            raise ValueError("Umur tidak valid.")
        if jenis_sim.upper() not in ("A", "C"):
            raise ValueError("Jenis SIM harus A atau C.")
        self.nama = nama                          # PUBLIC
        self.__nik = nik                          # PRIVATE
        self.__umur = umur                        # PRIVATE
        self.__jenis_sim = jenis_sim.upper()      # PRIVATE

    @property
    def umur(self):
        return self.__umur

    @property
    def jenis_sim(self):
        return self.__jenis_sim

    def punya_sim(self, jenis):
        return self.__jenis_sim == jenis

    def nik_tersamar(self):
        return self.__nik[:4] + "*" * max(len(self.__nik) - 6, 0) + self.__nik[-2:]

    def ke_dict(self):
        """Ekspor untuk JSON (satu-satunya jalan keluar NIK privat)."""
        return {"nama": self.nama, "nik": self.__nik,
                "umur": self.__umur, "jenis_sim": self.__jenis_sim}


# ======================================================================
# 7. PEMBAYARAN  -  komponen (Enkapsulasi)
# ======================================================================
class Pembayaran:
    """Uang & status lunas privat; hanya berubah lewat bayar() / selesaikan()."""

    def __init__(self, metode):
        self.metode = metode              # PUBLIC
        self.__biaya_sewa = 0.0           # PRIVATE
        self.__deposit = 0.0              # PRIVATE
        self.__lunas = False              # PRIVATE

    @property
    def biaya_sewa(self):
        return self.__biaya_sewa

    @property
    def deposit(self):
        return self.__deposit

    @property
    def lunas(self):
        return self.__lunas

    def bayar(self, biaya, deposit):
        if biaya <= 0:
            return False
        self.__biaya_sewa = biaya
        self.__deposit = deposit
        self.__lunas = True
        return True

    def selesaikan(self, denda):
        """Denda dipotong dari deposit. Return (deposit_kembali, kekurangan)."""
        dipotong = min(denda, self.__deposit)
        kembali = self.__deposit - dipotong
        kurang = denda - dipotong
        self.__deposit = 0.0
        return kembali, kurang


# ======================================================================
# 6. RENTAL  -  transaksi sewa (Asosiasi)
# ======================================================================
class Rental:
    """
    Menghubungkan semua pihak dalam satu transaksi:
      - Agregasi : Pelanggan & KendaraanRental (sudah ada sebelum rental dibuat)
      - Komposisi: Pembayaran (dibuat di dalam, hidup-matinya mengikuti Rental)
    """

    MAKS_HARI = 30

    def __init__(self, nomor, pelanggan, kendaraan, lama_hari, metode, tanggal_mulai=None):
        self.__nomor = nomor                                           # PRIVATE
        self.pelanggan = pelanggan                                     # PUBLIC (agregasi)
        self.kendaraan = kendaraan                                     # PUBLIC (agregasi)
        self.pembayaran = Pembayaran(metode)                           # PUBLIC (komposisi)
        self.__tanggal_mulai = tanggal_mulai or datetime.date.today()  # PRIVATE
        self.__lama_hari = 0                                           # PRIVATE
        self.lama_hari = lama_hari                                     # lewat setter
        self.__status = "MENUNGGU"                                     # PRIVATE
        self.__alasan_tolak = ""                                       # PRIVATE
        self.__biaya_sewa = 0.0
        self.__deposit = 0.0
        self.__hari_terlambat = 0
        self.__denda = 0.0
        self.__deposit_kembali = 0.0
        self.__kekurangan = 0.0

    # ---------- akses terkontrol ----------
    @property
    def nomor(self):
        return self.__nomor

    @property
    def status(self):
        return self.__status

    @property
    def alasan_tolak(self):
        return self.__alasan_tolak

    @property
    def lama_hari(self):
        return self.__lama_hari

    @lama_hari.setter
    def lama_hari(self, nilai):
        if not 1 <= nilai <= self.MAKS_HARI:
            raise ValueError(f"Lama sewa harus 1-{self.MAKS_HARI} hari.")
        self.__lama_hari = nilai

    @property
    def tanggal_mulai(self):
        return self.__tanggal_mulai

    @property
    def tanggal_kembali(self):
        return self.__tanggal_mulai + datetime.timedelta(days=self.__lama_hari)

    @property
    def biaya_sewa(self):
        return self.__biaya_sewa

    @property
    def deposit(self):
        return self.__deposit

    @property
    def hari_terlambat(self):
        return self.__hari_terlambat

    @property
    def denda(self):
        return self.__denda

    @property
    def deposit_kembali(self):
        return self.__deposit_kembali

    @property
    def kekurangan(self):
        return self.__kekurangan

    @property
    def pendapatan(self):
        return self.__biaya_sewa + self.__denda

    # ---------- proses ----------
    def mulai(self):
        """Polimorfisme: biaya, deposit & syarat dihitung oleh objek kendaraan."""
        if self.__status != "MENUNGGU":
            return False, "Pesanan sudah ditanggapi."
        if not self.kendaraan.tersedia():
            return False, "Kendaraan sedang disewa."
        boleh, alasan = self.kendaraan.validasi_pelanggan(self.pelanggan)
        if not boleh:
            return False, alasan
        biaya = self.kendaraan.hitung_biaya_sewa(self.__lama_hari)
        deposit = self.kendaraan.hitung_deposit()
        self.pembayaran.bayar(biaya, deposit)
        self.__biaya_sewa, self.__deposit = biaya, deposit
        self.kendaraan.tandai_disewa()
        self.__status = "AKTIF"
        return True, ""

    def tolak(self, alasan):
        if self.__status != "MENUNGGU":
            return False
        self.__alasan_tolak = alasan
        self.__status = "DITOLAK"
        return True

    def selesai(self, hari_terlambat=0):
        if self.__status != "AKTIF":
            return False
        self.__hari_terlambat = hari_terlambat
        self.__denda = self.kendaraan.hitung_denda(hari_terlambat)
        self.__deposit_kembali, self.__kekurangan = self.pembayaran.selesaikan(self.__denda)
        self.kendaraan.tandai_tersedia()
        self.__status = "SELESAI"
        return True

    # ---------- JSON ----------
    def ke_dict(self):
        return {
            "nomor": self.__nomor,
            "pelanggan": self.pelanggan.ke_dict(),
            "plat_nomor": self.kendaraan.plat_nomor,
            "metode": self.pembayaran.metode,
            "tanggal_mulai": self.__tanggal_mulai.isoformat(),
            "lama_hari": self.__lama_hari,
            "status": self.__status,
            "biaya_sewa": self.__biaya_sewa,
            "deposit": self.__deposit,
            "hari_terlambat": self.__hari_terlambat,
            "denda": self.__denda,
            "alasan_tolak": self.__alasan_tolak,
        }

    @staticmethod
    def dari_dict(d, peta_kendaraan):
        """Bangun ulang Rental dari JSON memakai constructor & method resmi."""
        kendaraan = peta_kendaraan.get(d["plat_nomor"])
        if kendaraan is None:
            return None
        p = d["pelanggan"]
        pelanggan = Pelanggan(p["nama"], p["nik"], p["umur"], p["jenis_sim"])
        r = Rental(d["nomor"], pelanggan, kendaraan, d["lama_hari"], d["metode"],
                   datetime.date.fromisoformat(d["tanggal_mulai"]))
        r.__status = d["status"]
        if r.__status in ("AKTIF", "SELESAI"):
            r.pembayaran.bayar(d["biaya_sewa"], d["deposit"])
            r.__biaya_sewa, r.__deposit = d["biaya_sewa"], d["deposit"]
        if r.__status == "AKTIF":
            kendaraan.tandai_disewa()
        elif r.__status == "SELESAI":
            r.__hari_terlambat, r.__denda = d["hari_terlambat"], d["denda"]
            r.__deposit_kembali, r.__kekurangan = r.pembayaran.selesaikan(r.__denda)
        elif r.__status == "DITOLAK":
            r.__alasan_tolak = d.get("alasan_tolak", "")
        return r


# ======================================================================
# 9. NOTA  -  bukti transaksi (Asosiasi ke Rental)
# ======================================================================
class Nota:
    """Bukti transaksi. Hanya membaca data dari Rental (tidak mengubahnya)."""

    def __init__(self, rental, jenis):
        self.__rental = rental                                 # PRIVATE (asosiasi)
        self.__jenis = jenis                                   # "SEWA" / "KEMBALI"
        self.__nomor_nota = f"NT-{rental.nomor}-{jenis[0]}"    # PRIVATE
        self.__waktu = datetime.datetime.now()                 # PRIVATE

    @property
    def nomor_nota(self):
        return self.__nomor_nota

    def baris(self):
        r, k = self.__rental, self.__rental.kendaraan
        garis = "-" * 46
        isi = [
            "=" * 46,
            f"NOTA {self.__jenis} KENDARAAN".center(46),
            "=" * 46,
            f"No. Nota   : {self.__nomor_nota}",
            f"Waktu      : {self.__waktu:%Y-%m-%d %H:%M}",
            f"Pelanggan  : {r.pelanggan.nama} (NIK {r.pelanggan.nik_tersamar()})",
            f"Kendaraan  : {k.merek} [{k.plat_nomor}] - {k.jenis_kendaraan()}",
            f"Periode    : {r.tanggal_mulai} s/d {r.tanggal_kembali} ({r.lama_hari} hari)",
            garis,
        ]
        if self.__jenis == "SEWA":
            isi += [
                f"Biaya sewa : {rupiah(r.biaya_sewa):>20}",
                f"Deposit    : {rupiah(r.deposit):>20}",
                f"TOTAL BAYAR: {rupiah(r.biaya_sewa + r.deposit):>20}",
                f"Metode     : {r.pembayaran.metode}",
            ]
        else:
            isi += [
                f"Terlambat  : {r.hari_terlambat} hari",
                f"Denda      : {rupiah(r.denda):>20}",
                f"Kembali DP : {rupiah(r.deposit_kembali):>20}",
            ]
            if r.kekurangan > 0:
                isi.append(f"Kekurangan : {rupiah(r.kekurangan):>20}")
        isi.append("=" * 46)
        return isi

    def cetak(self):
        for b in self.baris():
            print("  " + b)


# ======================================================================
# 8. ADMIN  -  pengelola rental (Enkapsulasi & Asosiasi)
# ======================================================================
class Admin:
    """Menanggapi pesanan online (setujui/tolak), mengelola armada & transaksi, simpan/muat JSON."""

    def __init__(self, username, password, file_data):
        self.username = username              # PUBLIC
        self.__password = password            # PRIVATE
        self.file_data = file_data            # PUBLIC
        self._armada = []                     # PROTECTED (agregasi KendaraanRental)
        self._daftar_rental = []              # PROTECTED (agregasi Rental)
        self._nomor_terakhir = 0              # PROTECTED

    def login(self, password):
        return password == self.__password

    @property
    def armada(self):
        return list(self._armada)

    @property
    def daftar_rental(self):
        return list(self._daftar_rental)

    def tambah_kendaraan(self, kendaraan):
        self._armada.append(kendaraan)

    def kendaraan_tersedia(self):
        return [k for k in self._armada if k.tersedia()]

    def rental_aktif(self):
        return [r for r in self._daftar_rental if r.status == "AKTIF"]

    def terima_pesanan(self, pelanggan, kendaraan, lama_hari, metode):
        """Pesanan online masuk -> berstatus MENUNGGU tanggapan admin."""
        nomor = f"RNT-{self._nomor_terakhir + 1:03d}"
        try:
            rental = Rental(nomor, pelanggan, kendaraan, lama_hari, metode)
        except ValueError as e:
            return None, str(e)
        self._nomor_terakhir += 1
        self._daftar_rental.append(rental)
        self.simpan()
        return rental, ""

    def pesanan_menunggu(self):
        return [r for r in self._daftar_rental if r.status == "MENUNGGU"]

    def cari_rental(self, nomor):
        for r in self._daftar_rental:
            if r.nomor == nomor:
                return r
        return None

    def tanggapi_pesanan(self, rental, setuju, catatan=""):
        """Admin menanggapi pesanan. Return (disetujui, pesan)."""
        if rental.status != "MENUNGGU":
            return False, "Pesanan sudah ditanggapi."
        if setuju:
            ok, alasan = rental.mulai()
            if not ok:                       # syarat/ketersediaan tidak terpenuhi
                rental.tolak(alasan)
                self.simpan()
                return False, f"Otomatis ditolak: {alasan}"
            self.simpan()
            return True, "Pesanan disetujui."
        rental.tolak(catatan or "Ditolak oleh admin.")
        self.simpan()
        return False, "Pesanan ditolak."

    def proses_kembali(self, rental, hari_terlambat):
        if not rental.selesai(hari_terlambat):
            return False
        self.simpan()
        return True

    def buat_nota(self, rental, jenis):
        return Nota(rental, jenis)

    def total_pendapatan(self):
        return sum(r.pendapatan for r in self._daftar_rental
                   if r.status in ("AKTIF", "SELESAI"))

    # ---------- JSON ----------
    def simpan(self):
        data = {"rental": [r.ke_dict() for r in self._daftar_rental],
                "nomor_terakhir": self._nomor_terakhir}
        with open(self.file_data, "w", encoding="utf-8") as f:
            json.dump(data, f, indent=2, ensure_ascii=False)

    def muat(self):
        """Return pesan status untuk ditampilkan."""
        if not os.path.exists(self.file_data):
            return "Belum ada file data, mulai dari kosong."
        try:
            with open(self.file_data, encoding="utf-8") as f:
                data = json.load(f)
            peta = {k.plat_nomor: k for k in self._armada}
            hasil = [Rental.dari_dict(d, peta) for d in data.get("rental", [])]
            self._daftar_rental = [r for r in hasil if r is not None]
            self._nomor_terakhir = data.get("nomor_terakhir", len(self._daftar_rental))
            return (f"Data dimuat dari {os.path.basename(self.file_data)} "
                    f"({len(self._daftar_rental)} rental).")
        except (json.JSONDecodeError, KeyError, TypeError, ValueError, OSError) as e:
            return f"[PERINGATAN] Gagal memuat data ({e}). Mulai dari kosong."


# ======================================================================
# 10. MAIN  -  Driver
# ======================================================================
class Main:
    METODE_BAYAR = ["Tunai", "Transfer", "QRIS", "E-Wallet"]
    FILE_DATA = os.path.join(os.path.dirname(os.path.abspath(__file__)), "data_rental.json")
    admin: Admin = None

    # ---------------- helper input ----------------
    @staticmethod
    def judul(teks):
        print("\n" + "=" * 78)
        print(teks)
        print("=" * 78)

    @staticmethod
    def input_teks(prompt):
        while True:
            nilai = input(prompt).strip()
            if nilai:
                return nilai
            print("  Input tidak boleh kosong.")

    @staticmethod
    def input_bulat(prompt, minimum=0, maksimum=None):
        while True:
            mentah = input(prompt).strip()
            if not mentah.lstrip("-").isdigit():
                print("  Masukkan bilangan bulat.")
                continue
            nilai = int(mentah)
            if nilai < minimum or (maksimum is not None and nilai > maksimum):
                batas = f"{minimum}-{maksimum}" if maksimum is not None else f">= {minimum}"
                print(f"  Nilai harus {batas}.")
                continue
            return nilai

    @staticmethod
    def input_nik(prompt):
        while True:
            nilai = input(prompt).strip()
            if nilai.isdigit() and len(nilai) == 16:
                return nilai
            print("  NIK harus 16 digit angka.")

    @staticmethod
    def pilih(prompt, opsi):
        for i, teks in enumerate(opsi, 1):
            print(f"   {i}. {teks}")
        while True:
            mentah = input(prompt).strip()
            if mentah.isdigit() and 1 <= int(mentah) <= len(opsi):
                return int(mentah) - 1
            print(f"  Pilih angka 1-{len(opsi)}.")

    # ---------------- persiapan ----------------
    @staticmethod
    def siapkan_data():
        Main.admin = Admin("admin", "admin123", Main.FILE_DATA)
        for k in (
            Mobil("L 1234 AB", "Toyota Avanza", 2022, 400_000, 4),
            Mobil("L 4321 CD", "Mitsubishi Xpander", 2023, 450_000, 4),
            Mobil("L 7777 FT", "Toyota Fortuner", 2023, 900_000, 4),
            Motor("L 5678 EF", "Honda Vario 125", 2022, 100_000, 125),
            Motor("L 8765 GH", "Yamaha NMAX", 2023, 150_000, 155),
            Motor("L 2468 PX", "Honda PCX", 2023, 175_000, 160),
        ):
            Main.admin.tambah_kendaraan(k)
        print("  " + Main.admin.muat())

    @staticmethod
    def login():
        for sisa in (3, 2, 1):
            if Main.admin.login(input("  Password admin (default: admin123): ").strip()):
                print(f"  Selamat datang, {Main.admin.username}!")
                return True
            print(f"  Password salah. Sisa percobaan: {sisa - 1}")
        return False

    @staticmethod
    def jalankan_menu(judul, menu, aksi):
        """Loop menu generik. Pilihan terakhir selalu 'kembali'."""
        while True:
            print(f"\n{judul}")
            pilihan = Main.pilih("Pilih menu: ", menu)
            if pilihan == len(menu) - 1:
                return
            aksi[pilihan]()

    # ================= MENU BERSAMA =================
    @staticmethod
    def lihat_kendaraan():
        Main.judul("DAFTAR KENDARAAN (POLIMORFISME)")
        for i, k in enumerate(Main.admin.armada, 1):
            print(f"  {i}. {k.ringkas()}")

    @staticmethod
    def bandingkan_biaya():
        Main.judul("BANDINGKAN BIAYA SEMUA KENDARAAN")
        lama = Main.input_bulat(f"  Lama sewa (1-{Rental.MAKS_HARI} hari): ", 1, Rental.MAKS_HARI)
        print()
        for k in Main.admin.armada:     # satu loop, hasil berbeda tiap objek
            print(f"  {k.merek:<18} {k.jenis_kendaraan():<6} "
                  f"Sewa {rupiah(k.hitung_biaya_sewa(lama)):>11} | "
                  f"Deposit {rupiah(k.hitung_deposit()):>10} | "
                  f"Denda/hari {rupiah(k.hitung_denda(1)):>10}")

    # ================= MENU PELANGGAN (ONLINE) =================
    @staticmethod
    def buat_pesanan():
        Main.judul("PESAN KENDARAAN ONLINE")
        daftar = Main.admin.armada
        print("  Pilih kendaraan:")
        kendaraan = daftar[Main.pilih("  Kendaraan: ", [k.ringkas() for k in daftar])]

        print("\n  Data pemesan:")
        nama = Main.input_teks("  Nama           : ")
        nik = Main.input_nik("  NIK (16 digit) : ")
        umur = Main.input_bulat("  Umur           : ", 1, 100)
        print("  Jenis SIM:")
        sim = ["A", "C"][Main.pilih("  Pilih SIM: ", ["SIM A (mobil)", "SIM C (motor)"])]
        lama = Main.input_bulat(f"  Lama sewa (1-{Rental.MAKS_HARI} hari): ", 1, Rental.MAKS_HARI)
        print("  Metode pembayaran:")
        metode = Main.METODE_BAYAR[Main.pilih("  Metode: ", Main.METODE_BAYAR)]

        biaya = kendaraan.hitung_biaya_sewa(lama)
        deposit = kendaraan.hitung_deposit()
        print(f"\n  Estimasi biaya sewa : {rupiah(biaya)}")
        print(f"  Deposit             : {rupiah(deposit)}")
        print(f"  Estimasi total      : {rupiah(biaya + deposit)}")
        print("  (Pembayaran dilakukan setelah pesanan disetujui admin.)")
        if input("  Kirim pesanan? (y/n): ").strip().lower() != "y":
            print("  Dibatalkan.")
            return

        pelanggan = Pelanggan(nama, nik, umur, sim)
        rental, alasan = Main.admin.terima_pesanan(pelanggan, kendaraan, lama, metode)
        if rental is None:
            print(f"\n  [GAGAL] {alasan}")
            return
        print(f"\n  [TERKIRIM] Nomor pesanan Anda: {rental.nomor}")
        print("  Status: MENUNGGU tanggapan admin. Simpan nomor ini untuk cek status.")

    @staticmethod
    def cek_status():
        Main.judul("CEK STATUS PESANAN")
        nomor = Main.input_teks("  Nomor pesanan (contoh RNT-001): ").upper()
        r = Main.admin.cari_rental(nomor)
        if r is None:
            print("  Nomor pesanan tidak ditemukan.")
            return
        print(f"\n  Pesanan   : {r.nomor}")
        print(f"  Pemesan   : {r.pelanggan.nama}")
        print(f"  Kendaraan : {r.kendaraan.merek} [{r.kendaraan.plat_nomor}]")
        print(f"  Status    : {r.status}")
        if r.status == "MENUNGGU":
            print("  Pesanan Anda sedang menunggu tanggapan admin.")
        elif r.status == "DITOLAK":
            print(f"  Alasan    : {r.alasan_tolak}")
        else:
            print()
            Main.admin.buat_nota(r, "SEWA").cetak()

    # ================= MENU ADMIN =================
    @staticmethod
    def tanggapi_pesanan():
        Main.judul("PESANAN MASUK (MENUNGGU TANGGAPAN)")
        menunggu = Main.admin.pesanan_menunggu()
        if not menunggu:
            print("  Tidak ada pesanan yang menunggu.")
            return
        opsi = [f"{r.nomor} | {r.pelanggan.nama:<10} | {r.kendaraan.merek:<18} | {r.lama_hari} hari"
                for r in menunggu]
        r = menunggu[Main.pilih("  Pilih pesanan: ", opsi)]

        boleh, alasan = r.kendaraan.validasi_pelanggan(r.pelanggan)
        print(f"\n  Pesanan    : {r.nomor}")
        print(f"  Pemesan    : {r.pelanggan.nama} (NIK {r.pelanggan.nik_tersamar()}, "
              f"umur {r.pelanggan.umur}, SIM {r.pelanggan.jenis_sim})")
        print(f"  Kendaraan  : {r.kendaraan.merek} [{r.kendaraan.plat_nomor}] - "
              f"{'tersedia' if r.kendaraan.tersedia() else 'SEDANG DISEWA'}")
        print(f"  Lama sewa  : {r.lama_hari} hari | Metode: {r.pembayaran.metode}")
        print(f"  Estimasi   : {rupiah(r.kendaraan.hitung_biaya_sewa(r.lama_hari))} "
              f"+ deposit {rupiah(r.kendaraan.hitung_deposit())}")
        print(f"  Syarat     : {'terpenuhi' if boleh else 'TIDAK terpenuhi - ' + alasan}\n")

        setuju = Main.pilih("  Tanggapan: ", ["Setujui", "Tolak"]) == 0
        catatan = "" if setuju else input("  Alasan penolakan: ").strip()
        diterima, pesan = Main.admin.tanggapi_pesanan(r, setuju, catatan)
        print(f"\n  {pesan}")
        if diterima:
            print()
            Main.admin.buat_nota(r, "SEWA").cetak()

    @staticmethod
    def kembalikan_kendaraan():
        Main.judul("PENGEMBALIAN KENDARAAN")
        aktif = Main.admin.rental_aktif()
        if not aktif:
            print("  Tidak ada kendaraan yang sedang disewa.")
            return
        opsi = [f"{r.nomor} | {r.kendaraan.merek} [{r.kendaraan.plat_nomor}] | "
                f"{r.pelanggan.nama} | kembali {r.tanggal_kembali}" for r in aktif]
        rental = aktif[Main.pilih("  Pilih rental: ", opsi)]
        telat = Main.input_bulat("  Terlambat berapa hari (0 jika tepat waktu): ", 0, 60)

        Main.admin.proses_kembali(rental, telat)
        print()
        Main.admin.buat_nota(rental, "KEMBALI").cetak()

    @staticmethod
    def riwayat_rental():
        Main.judul("RIWAYAT PESANAN & RENTAL")
        daftar = Main.admin.daftar_rental
        if not daftar:
            print("  Belum ada transaksi.")
        for r in daftar:
            print(f"  {r.nomor} | {r.pelanggan.nama:<10} | {r.kendaraan.merek:<18} | "
                  f"{r.lama_hari:>2} hari | {rupiah(r.pendapatan):>12} | {r.status}")

    @staticmethod
    def cetak_ulang_nota():
        Main.judul("CETAK ULANG NOTA")
        daftar = [r for r in Main.admin.daftar_rental if r.status in ("AKTIF", "SELESAI")]
        if not daftar:
            print("  Belum ada transaksi yang disetujui.")
            return
        rental = daftar[Main.pilih("  Pilih rental: ",
                                   [f"{r.nomor} - {r.pelanggan.nama} ({r.status})" for r in daftar])]
        jenis = "SEWA"
        if rental.status == "SELESAI":
            jenis = ["SEWA", "KEMBALI"][Main.pilih("  Jenis nota: ", ["Nota sewa", "Nota kembali"])]
        print()
        Main.admin.buat_nota(rental, jenis).cetak()

    @staticmethod
    def laporan():
        Main.judul("LAPORAN")
        daftar = Main.admin.daftar_rental

        def hitung(status):
            return sum(1 for r in daftar if r.status == status)

        print(f"  Total pesanan       : {len(daftar)}")
        print(f"  Menunggu tanggapan  : {hitung('MENUNGGU')}")
        print(f"  Ditolak             : {hitung('DITOLAK')}")
        print(f"  Sedang berjalan     : {hitung('AKTIF')}")
        print(f"  Selesai             : {hitung('SELESAI')}")
        print(f"  Total pendapatan    : {rupiah(Main.admin.total_pendapatan())} (sewa + denda)")

    @staticmethod
    def demo_konsep():
        Main.judul("DEMO KONSEP OOP")
        for cls, args in ((Kendaraan, ("X", "Tes", 2020)),
                          (KendaraanRental, ("X", "Tes", 2020, 1000))):
            try:
                cls(*args)
            except TypeError as e:
                print(f"  [Abstract]    {cls.__name__} tidak bisa dibuat: {e}")
        k = Main.admin.armada[0]
        try:
            print(k.__plat_nomor)
        except AttributeError:
            print("  [Private]     k.__plat_nomor tidak bisa diakses dari luar class.")
        try:
            k.plat_nomor = "Z 0000 ZZ"
        except AttributeError:
            print("  [Property]    plat nomor hanya bisa dibaca, tidak bisa diubah.")
        print("  [Protected]   k._tarif_dasar =", k._tarif_dasar,
              "(bisa diakses, tapi konvensinya hanya untuk class & turunannya)")
        try:
            Rental("X", None, k, 0, "Tunai")
        except ValueError as e:
            print("  [Setter]      Rental(lama_hari=0) ditolak:", e)
        try:
            print(Main.admin.__password)
        except AttributeError:
            print("  [Private]     password admin tidak bisa dibaca dari luar.")
        print("  [Inheritance] MRO Mobil:",
              " -> ".join(c.__name__ for c in Mobil.__mro__[:-1]))

    # ================= ALUR UTAMA =================
    @staticmethod
    def menu_pelanggan():
        Main.jalankan_menu(
            "MENU PELANGGAN (PESAN ONLINE)",
            ["Lihat kendaraan", "Buat pesanan online", "Cek status pesanan",
             "Bandingkan biaya semua kendaraan", "Kembali"],
            [Main.lihat_kendaraan, Main.buat_pesanan, Main.cek_status, Main.bandingkan_biaya])

    @staticmethod
    def menu_admin():
        if not Main.login():
            print("  Login gagal.")
            return
        masuk = len(Main.admin.pesanan_menunggu())
        print(f"  Ada {masuk} pesanan yang menunggu tanggapan.")
        Main.jalankan_menu(
            "MENU ADMIN",
            ["Tanggapi pesanan masuk", "Kembalikan kendaraan", "Riwayat pesanan & rental",
             "Cetak ulang nota", "Laporan", "Lihat kendaraan", "Demo konsep OOP", "Logout"],
            [Main.tanggapi_pesanan, Main.kembalikan_kendaraan, Main.riwayat_rental,
             Main.cetak_ulang_nota, Main.laporan, Main.lihat_kendaraan, Main.demo_konsep])

    @staticmethod
    def jalankan():
        Main.judul("RENTAL KENDARAAN ONLINE")
        Main.siapkan_data()
        try:
            while True:
                print("\nMASUK SEBAGAI")
                peran = Main.pilih("Pilih: ", ["Pelanggan (pesan online)", "Admin", "Keluar"])
                if peran == 0:
                    Main.menu_pelanggan()
                elif peran == 1:
                    Main.menu_admin()
                else:
                    print("Terima kasih!")
                    break
        except (EOFError, KeyboardInterrupt):
            print("\nProgram dihentikan.")


if __name__ == "__main__":
    Main.jalankan()