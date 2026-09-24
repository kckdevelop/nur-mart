# Backend Toko Kelontong NURMART (Laravel 12 API)

Sistem backend RESTful API untuk aplikasi mobile Flutter pengelola Toko Kelontong (POS Kasir, Manajemen Stok/Inventaris, Kulakan/Belanja Supplier, dan Laporan Keuangan Laba Rugi).

## Dokumentasi Lengkap
Seluruh panduan integrasi, spesifikasi request/response endpoint, role user, serta contoh kode Dart/Flutter dapat dilihat pada:
📄 **[API_DOCUMENTATION.md](file:///e:/PROJECT%20MOBILE/NURMART/SERVER-NURMART/API_DOCUMENTATION.md)**

---

## Fitur Utama
1. **Autentikasi API**: Laravel Sanctum Token, refresh token, role management (`pemilik` dan `kasir`).
2. **Master Data (CRUD)**: Kategori, Supplier, dan Produk/Barang (pencarian nama/SKU/barcode & upload gambar).
3. **Purchasing (Belanja Barang)**: Pencatatan belanja dari supplier & otomatis menambah stok serta memperbarui harga beli.
4. **POS & Penjualan (Kasir)**: Transaksi penjualan dengan pengecekan stok atomik (`lockForUpdate`), otomatis potong stok, hitung kembalian, dan cetak struk PDF thermal roll (80mm/58mm) via Dompdf.
5. **Laporan & Dasbor**: Omset harian/bulanan, margin keuntungan, peringatan stok menipis, dan analisis laba rugi.

---

## Menjalankan Server
```bash
# Menjalankan server agar dapat diakses oleh HP/Emulator Flutter
php artisan serve --host=0.0.0.0 --port=8000
```

## Akun Uji Coba (Default Seeder)
- **Pemilik (Owner)**: `pemilik@nurmart.com` / `password123`
- **Kasir (Staff)**: `kasir@nurmart.com` / `password123`
