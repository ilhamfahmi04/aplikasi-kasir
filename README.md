# Sistem Kasir Toko Kelontong Berbasis Web

Aplikasi Kasir Toko Kelontong berbasis web yang modern, profesional, dan mudah digunakan untuk pemilik toko maupun petugas kasir. Dibangun dengan framework **Laravel**, antarmuka responsif **Tailwind CSS**, visualisasi grafik **Chart.js**, dan database **MySQL**.

---

## 🌟 Fitur Utama

### 1. Sistem Autentikasi & Hak Akses (Multi-Role)
- **Admin (Pemilik Toko):**
  - Akses penuh dashboard analitik & grafik keuangan.
  - CRUD Kategori dan Data Barang.
  - Manajemen pergerakan stok (Masuk, Keluar, dan Penyesuaian/Opname).
  - Monitoring barang kedaluwarsa & peringatan otomatis.
  - Riwayat lengkap transaksi seluruh kasir.
  - Rekap laporan penjualan mingguan, bulanan, cetak PDF, dan ekspor ke Excel (CSV).
  - Manajemen pengguna (tambah akun kasir, ubah status aktif/nonaktif).
- **Kasir:**
  - Dashboard performa kasir harian.
  - Terminal Transaksi Kasir (*Point of Sale* / POS) dengan dukungan scan barcode / pencarian live.
  - Perhitungan otomatis subtotal, total belanja, dan uang kembalian.
  - Cetak struk pembelian format thermal (58mm/80mm).
  - Riwayat transaksi kasir pribadi.

### 2. Dashboard Analitik & Visualisasi Data
- Kartu statistik: Penjualan Hari Ini, Minggu Ini, Bulan Ini, dan Total Keuntungan Bersih (Laba Kotor).
- Ringkasan stok menipis (di bawah batas minimum) dan jumlah barang kedaluwarsa.
- Grafik interaktif **Chart.js** dengan pilihan tampilan harian (7 hari terakhir) dan bulanan (6 bulan terakhir).

### 3. Monitoring Barang Kedaluwarsa (*Expiry Monitoring*)
- Deteksi barang kedaluwarsa secara otomatis:
  - ⚠️ **Waspada (≤ 30 Hari)**: Memberi peringatan dini untuk memutar barang.
  - 🚨 **Kritis (≤ 7 Hari)**: Rekomendasi promosi/diskon cuci gudang.
  - ⛔ **Sudah Kedaluwarsa**: Sistem kasir secara otomatis mengunci dan menolak penjualan barang ini demi kepatuhan dan keselamatan konsumen.
- Indikator lonceng notifikasi dinamis pada bilah navigasi (*navbar*).

### 4. Manajemen Stok Otomatis (*Stock Movements*)
- Stok berkurang otomatis setiap kali transaksi penjualan berhasil.
- Stok bertambah saat admin memasukkan stok masuk dari kulakan/suplier.
- Riwayat lengkap setiap pergerakan stok: waktu, nomor referensi/faktur, tipe pergerakan, dan petugas.
- Menggunakan **Database Transaction (`DB::transaction`)** dan *row-level locking* untuk mencegah inkonsistensi data atau selisih stok.

### 5. Rekap Laporan Penjualan & Ekspor
- Filter laporan berdasarkan **Mingguan**, **Bulanan**, atau **Rentang Tanggal Kustom**.
- Menampilkan rincian: Total Transaksi, Total Item Terjual, Total Omset, Total Modal Pokok, dan Total Keuntungan Bersih.
- Menampilkan 5 Produk Paling Laris (*Top Selling Products*) dan tanggal rekor penjualan tertinggi.
- Fitur **Cetak Dokumen Laporan / Simpan ke PDF**.
- Fitur **Export Laporan ke File Excel (CSV)**.

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP 8.2+ / Laravel 11+
- **Database:** MySQL / MariaDB
- **Frontend:** Blade Templating, HTML5, JavaScript
- **Styling:** Tailwind CSS, Bootstrap Icons
- **Libraries:** Chart.js, Alpine.js, SweetAlert2
- **Development Environment:** Laravel Herd / XAMPP di Windows

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repository
```bash
git clone https://github.com/ilhamfahmi04/aplikasi-kasir.git
cd aplikasi-kasir
```

### 2. Instal Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan pengaturan database:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kasir_kelontong
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Buat Database & Jalankan Migration + Seeder
Pastikan MySQL Anda sudah aktif (misal lewat XAMPP atau Laravel Herd), lalu jalankan:
```bash
php artisan migrate --seed
```

### 6. Jalankan Aplikasi
Jika menggunakan **Laravel Herd**, project otomatis dapat diakses di:
```
http://aplikasi-kasir.test
```
Atau menggunakan server bawaan Laravel:
```bash
php artisan serve
```
Buka browser di `http://localhost:8000`.

---

## 🔑 Akun Bawaan untuk Uji Coba

Setelah menjalankan `php artisan db:seed`, Anda dapat langsung masuk menggunakan akun default berikut:

| Peran (Role) | Email | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin@kelontong.com` | `admin123` | Akses penuh dashboard, barang, stok, laporan, dan pengaturan |
| **Kasir** | `kasir@kelontong.com` | `kasir123` | Akses POS kasir, transaksi, dan riwayat kasir |

---

## 🧪 Menjalankan Pengujian (Automated Tests)

Aplikasi telah dilengkapi rangkaian unit & feature test untuk memastikan keamanan autentikasi, middleware role, pemotongan stok otomatis, dan pencegahan penjualan barang kedaluwarsa:

```bash
php artisan test
```

---

## 📄 Lisensi
Project ini dibuat di bawah lisensi MIT.
