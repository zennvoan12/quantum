# 📱 Quantum Cell — E-Commerce & Rekomendasi Produk Algoritma Apriori

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)

Sistem Informasi E-Commerce penjualan aksesoris dan perangkat seluler berbasis web dengan integrasi **Sistem Rekomendasi Produk menggunakan Algoritma Apriori (Association Rule Mining)** untuk analisis pola pembelian konsumen (*Market Basket Analysis*).

Project ini dikembangkan sebagai implementasi **Tugas Akhir S1 Teknik Informatika**.

---

## ✨ Fitur Utama

### 🛒 Storefront & Transaksi (Customer)
- **Katalog Produk & Kategori**: Navigasi produk interaktif berbasis slug dengan pencarian dan filter kategori.
- **Keranjang Belanja (Cart) & Wishlist**: Manajemen keranjang belanja real-time.
- **Sistem Checkout & Pembayaran**: Integrasi Payment Gateway (Midtrans Snap) dan upload bukti transfer manual.
- **Sistem Rekomendasi Apriori**: Rekomendasi produk terkait (*"Pelanggan yang membeli produk ini juga membeli..."*) di halaman detail produk berdasarkan nilai *Confidence* dan *Lift Ratio* tertinggi.
- **Riwayat & Pelacakan Pesanan**: Cek status transaksi, resi, dan invoice.

### 📊 Dashboard & Manajemen (Admin)
- **Ringkasan Penjualan**: Statistik omzet, pesanan baru, stok kritis, dan total pelanggan.
- **Manajemen Katalog**: CRUD Produk, Kategori, Foto Produk, dan Status Ketersediaan.
- **Manajemen Pesanan**: Verifikasi status pembayaran, update status pengiriman, dan cetak invoice.
- **Data Mining / Analisis Apriori Engine**:
  - Pengaturan ambang batas (*Minimum Support* & *Minimum Confidence*).
  - Eksekusi mining data transaksi penjualan historis.
  - Tabel hasil Frequent Itemsets ($k$-itemsets) dan *Association Rules*.
  - Metrik evaluasi rule: *Support*, *Confidence*, dan *Lift Ratio* (> 1.0 valid/positif).

---

## 🛠️ Tech Stack

- **Backend Framework**: Laravel 11 (PHP 8.3)
- **Database**: MySQL 8.0 (Development & Production) / SQLite (Automated PHPUnit Testing)
- **Frontend / UI**: Blade Templates, Tailwind CSS (Responsive Mobile-First), Alpine.js / Vanilla JS
- **Payment Gateway**: Midtrans Snap Integration
- **Testing**: PHPUnit / Pest Test Suite

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP >= 8.3 (dengan ekstensi `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `curl`)
- Composer >= 2.x
- MySQL Server 8.0
- Git

### 2. Clone Repositori & Setup Dependencies
```bash
git clone https://github.com/username/quantum-cell.git
cd quantum-cell
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin template environment dan sesuaikan konfigurasi database:
```bash
cp .env.example .env
php artisan key:generate
```

Ubah baris database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quantum_cell
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Data Seeding
Jalankan migrasi database beserta data dummy/pengujian (termasuk sample 200+ data transaksi untuk analisis Apriori):
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Server
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000` atau virtual host Laragon di `http://quantum.test`.

---

## 🔑 Akun Uji Coba Default

| Role | Email | Password | Akses URL |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@quantum.com` | `password` | `/admin` |
| **Customer** | `pembeli@quantum.com` | `password` | `/` |

---

## 🧠 Algoritma Apriori — Logika & Formula

Sistem menghitung keterkaitan antar produk yang sering dibeli bersamaan dalam satu transaksi:

1. **Support ($A \Rightarrow B$)**:
   $$\text{Support}(A \cup B) = \frac{\sum \text{Transaksi mengandung } A \text{ dan } B}{\text{Total Transaksi}}$$

2. **Confidence ($A \Rightarrow B$)**:
   $$\text{Confidence}(A \Rightarrow B) = \frac{\sum \text{Transaksi mengandung } A \text{ dan } B}{\sum \text{Transaksi mengandung } A}$$

3. **Lift Ratio ($A \Rightarrow B$)**:
   $$\text{Lift Ratio}(A \Rightarrow B) = \frac{\text{Confidence}(A \Rightarrow B)}{\text{Support}(B)}$$
   *Aturan asosiasi valid dan saling memicu jika $\text{Lift Ratio} > 1.0$.*

---

## 🧪 Pengujian Sistem (Automated Tests)

Jalankan rangkaian test otomatis dengan PHPUnit (menggunakan SQLite memory database):
```bash
php artisan test
```

---

## 📄 Lisensi & Hak Cipta
Dikembangkan oleh **Muhammad Farhan** untuk pemenuhan Tugas Akhir Sarjana Teknik Informatika. Dilisensikan di bawah [MIT License](LICENSE).
