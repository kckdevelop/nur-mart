# Dokumentasi Penggunaan API & Panduan User - Toko Kelontong NURMART

Dokumentasi ini disusun untuk developer aplikasi mobile (Flutter) dan tim teknis backend yang mengelola sistem backend Toko Kelontong **NURMART** berbasis **Laravel 12**.

---

## 1. Panduan Server & Koneksi

### Base URL
- **Localhost (Web / Postman):** `http://localhost:8000/api`
- **Android Emulator (Default):** `http://10.0.2.2:8000/api`
- **Real Device Android/iOS (Satu Jaringan Wi-Fi):** `http://<IP_KOMPUTER_SERVER>:8000/api`
  > *Contoh:* `http://192.168.1.10:8000/api`

### Menjalankan Server
Untuk mengizinkan koneksi dari HP / Emulator Flutter:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Format Header HTTP Wajib
Kecuali endpoint `/login`, seluruh request wajib menyertakan header berikut:
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <TOKEN_SANCTUM_ANDA>
```
*(Khusus endpoint upload gambar produk, gunakan `multipart/form-data`)*

---

## 2. Format Respon JSON Standar

Semua endpoint mengembalikan struktur JSON yang seragam untuk mempermudah pembuatan model di Flutter:

### Respon Sukses (HTTP 200 / 201)
```json
{
  "status": true,
  "message": "Operasi berhasil dilakukan",
  "data": { ... }
}
```

### Respon Validasi Gagal (HTTP 422)
```json
{
  "status": false,
  "message": "Validasi data gagal",
  "errors": {
    "kode_sku": [
      "Kode SKU sudah terdaftar."
    ]
  }
}
```

### Respon Tidak Diizinkan / Unauthenticated (HTTP 401 & 403)
```json
{
  "status": false,
  "message": "Akses ditolak: Anda tidak memiliki wewenang untuk fitur ini."
}
```

---

## 3. Manajemen User & Hak Akses (Role)

Sistem membedakan pengguna menjadi 2 peran (*roles*):

| Peran (Role) | Deskripsi | Hak Akses Fitur |
| :--- | :--- | :--- |
| **`pemilik`** | Pemilik Toko (Owner) | Memiliki akses penuh: CRUD Master Data, Pembelian Belanja ke Supplier, Akses Laporan Laba Rugi & Dasbor Keuangan. |
| **`kasir`** | Operator Kasir (Staff) | Akses transaksi POS Penjualan, Cetak Struk Belanja, Baca Master Data Barang (Read-only untuk scan barcode & pencarian produk). |

### Akun Uji Coba Bawaan (Default Seeder)

| Email | Password | Role | Keterangan |
| :--- | :--- | :--- | :--- |
| `pemilik@nurmart.com` | `password123` | `pemilik` | Akun Owner untuk manajemen toko & laporan |
| `kasir@nurmart.com` | `password123` | `kasir` | Akun Kasir untuk operasional POS harian |

> **Catatan Membuat User Baru:**  
> Untuk membuat user baru, jalankan perintah Tinker:
> ```bash
> php artisan tinker
> ```
> Lalu eksekusi:
> ```php
> App\Models\User::create([
>     'name' => 'Budi Santoso',
>     'email' => 'budi@nurmart.com',
>     'password' => bcrypt('rahasia123'),
>     'role' => 'kasir' // atau 'pemilik'
> ]);
> ```

---

## 4. Matriks Akses Endpoint

| Modul | Endpoint | Method | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Auth** | `/api/login` | `POST` | Publik |
| | `/api/profile` | `GET` | Kasir & Pemilik |
| | `/api/refresh-token` | `POST` | Kasir & Pemilik |
| | `/api/logout` | `POST` | Kasir & Pemilik |
| **Kategori** | `/api/kategori` | `GET` | Kasir & Pemilik |
| | `/api/kategori/{id}` | `GET` | Kasir & Pemilik |
| | `/api/kategori` | `POST` | **Pemilik Saja** |
| | `/api/kategori/{id}` | `PUT` | **Pemilik Saja** |
| | `/api/kategori/{id}` | `DELETE` | **Pemilik Saja** |
| **Supplier** | `/api/supplier` | `GET` | Kasir & Pemilik |
| | `/api/supplier/{id}` | `GET` | Kasir & Pemilik |
| | `/api/supplier` | `POST` | **Pemilik Saja** |
| | `/api/supplier/{id}` | `PUT` | **Pemilik Saja** |
| | `/api/supplier/{id}` | `DELETE` | **Pemilik Saja** |
| **Barang** | `/api/barang` | `GET` | Kasir & Pemilik |
| | `/api/barang/stok-menipis`| `GET` | Kasir & Pemilik |
| | `/api/barang/{id}` | `GET` | Kasir & Pemilik |
| | `/api/barang` | `POST` | **Pemilik Saja** |
| | `/api/barang/{id}` | `POST` / `PUT`| **Pemilik Saja** |
| | `/api/barang/{id}` | `DELETE` | **Pemilik Saja** |
| **Purchasing** | `/api/belanja` | `GET` | **Pemilik Saja** |
| | `/api/belanja` | `POST` | **Pemilik Saja** *(Otomatis Tambah Stok)* |
| | `/api/belanja/{id}` | `GET` | **Pemilik Saja** |
| **Sales POS** | `/api/penjualan` | `GET` | Kasir & Pemilik |
| | `/api/penjualan` | `POST` | Kasir & Pemilik *(Otomatis Potong Stok)* |
| | `/api/penjualan/{id}` | `GET` | Kasir & Pemilik |
| | `/api/penjualan/{id}/cetak-struk` | `GET` | Kasir & Pemilik *(Download PDF Struk)* |
| **Laporan** | `/api/laporan/dasbor` | `GET` | **Pemilik Saja** |
| | `/api/laporan/laba-rugi` | `GET` | **Pemilik Saja** |

---

## 5. Rincian API Endpoint

### 5.1. Autentikasi

#### [POST] `/api/login`
Digunakan oleh aplikasi Flutter untuk mengautentikasi pengguna dan memperoleh token akses.
- **Request Body:**
  ```json
  {
    "email": "kasir@nurmart.com",
    "password": "password123",
    "device_name": "Flutter Android Kasir 1"
  }
  ```
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Login berhasil.",
    "data": {
      "user": {
        "id": 2,
        "name": "Siti Aminah (Kasir)",
        "email": "kasir@nurmart.com",
        "role": "kasir"
      },
      "token": "1|8A7g89...dF6h9",
      "token_type": "Bearer"
    }
  }
  ```

#### [GET] `/api/profile`
Mendapatkan informasi akun pengguna yang sedang login.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Data profil berhasil diambil.",
    "data": {
      "id": 2,
      "name": "Siti Aminah (Kasir)",
      "email": "kasir@nurmart.com",
      "role": "kasir",
      "created_at": "2026-09-24T06:05:00.000000Z"
    }
  }
  ```

#### [POST] `/api/refresh-token`
Memperbarui token yang sedang aktif dan menghapus token lama.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Token berhasil diperbarui (refreshed).",
    "data": {
      "token": "2|9Z6q21...kL4m8",
      "token_type": "Bearer"
    }
  }
  ```

#### [POST] `/api/logout`
Membatalkan sesi token yang sedang aktif.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Logout berhasil, sesi telah dihapus.",
    "data": null
  }
  ```

---

### 5.2. Master Data Barang / Produk

#### [GET] `/api/barang`
Mendukung pagination, pencarian nama, barcode, atau SKU.
- **Query Parameters:**
  - `search` (opsional): cari berdasarkan nama / barcode / sku.
  - `kategori_id` (opsional): filter berdasarkan ID kategori.
  - `per_page` (opsional): default 15.
  - `all` (opsional): isi `true` jika Flutter ingin memuat seluruh barang untuk penyimpanan offline / cache lokal.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Daftar produk berhasil diambil.",
    "data": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "kode_sku": "BRG-0001",
          "barcode": "8992345100012",
          "nama_barang": "Beras Pandan Wangi 5kg",
          "kategori_id": 1,
          "harga_beli": 68000,
          "harga_jual": 76000,
          "stok": 25,
          "satuan": "dus/karung",
          "gambar_url": null,
          "kategori": {
            "id": 1,
            "nama_kategori": "Sembako"
          }
        }
      ],
      "total": 5
    }
  }
  ```

#### [GET] `/api/barang/stok-menipis`
Mengambil produk-produk yang stoknya sudah mencapai batas minimal.
- **Query Parameters:** `threshold` (opsional, default 10).
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Daftar stok barang menipis berhasil diambil.",
    "data": {
      "threshold": 10,
      "total_items": 1,
      "items": [
        {
          "id": 4,
          "kode_sku": "BRG-0004",
          "nama_barang": "Teh Botol Sosro Kotak 250ml",
          "stok": 4,
          "satuan": "pcs"
        }
      ]
    }
  }
  ```

#### [POST] `/api/barang` *(Pemilik)*
Menambah produk baru. Mendukung upload gambar file (multipart/form-data).
- **Form Data Parameters:**
  - `kode_sku` (string, required, unique)
  - `barcode` (string, optional)
  - `nama_barang` (string, required)
  - `kategori_id` (integer, required)
  - `harga_beli` (numeric, required)
  - `harga_jual` (numeric, required)
  - `stok` (integer, optional, default 0)
  - `satuan` (string, required, contoh: `pcs`, `kg`, `dus`, `pack`)
  - `gambar` (file image: jpg, jpeg, png, webp, max 2MB, optional)

---

### 5.3. Belanja Barang / Purchasing (Owner)

Fitur ini digunakan saat pemilik toko membeli pasokan/kulakan dari supplier.
Sistem secara otomatis **menambahkan stok barang** dan **mengupdate harga beli terbaru** pada database dalam satu transaksi aman (`DB::transaction`).

#### [POST] `/api/belanja` *(Pemilik)*
- **Request Body:**
  ```json
  {
    "supplier_id": 1,
    "no_faktur_pembelian": "INV-SUP-2026-009", // opsional, otomatis di-generate jika kosong
    "tanggal": "2026-09-24",
    "catatan": "Kulakan sembako awal pekan",
    "items": [
      {
        "barang_id": 1,
        "jumlah": 20,
        "harga_beli_satuan": 68000
      },
      {
        "barang_id": 3,
        "jumlah": 30,
        "harga_beli_satuan": 15000
      }
    ]
  }
  ```
- **Response 201 Created:**
  ```json
  {
    "status": true,
    "message": "Transaksi belanja berhasil disimpan dan stok barang otomatis bertambah.",
    "data": {
      "id": 1,
      "no_faktur_pembelian": "PB-20260924-0001",
      "supplier_id": 1,
      "user_id": 1,
      "tanggal": "2026-09-24",
      "total_belanja": 1810000,
      "catatan": "Kulakan sembako awal pekan",
      "supplier": {
        "id": 1,
        "nama_supplier": "PT Sumber Pangan Sejahtera"
      },
      "details": [
        {
          "id": 1,
          "barang_id": 1,
          "jumlah": 20,
          "harga_beli_satuan": 68000,
          "subtotal": 1360000,
          "barang": {
            "id": 1,
            "nama_barang": "Beras Pandan Wangi 5kg",
            "stok": 45
          }
        }
      ]
    }
  }
  ```

---

### 5.4. Fitur POS / Kasir & Penjualan (Sales)

Fitur ini digunakan oleh kasir pada saat melayani transaksi penjualan di toko.

#### [POST] `/api/penjualan` *(Kasir & Pemilik)*
- **Fitur Otomatis:**
  1. Validasi kecukupan stok secara atomik (`lockForUpdate`).
  2. Jika stok tidak mencukupi, transaksi ditolak dan mengembalikan HTTP 422 beserta nama produk yang kurang.
  3. Memotong stok barang secara otomatis.
  4. Menghitung kembalian berdasarkan `jumlah_bayar - total_belanja`.

- **Request Body:**
  ```json
  {
    "metode_pembayaran": "tunai", // opsi: "tunai" atau "qris"
    "jumlah_bayar": 100000,
    "items": [
      {
        "barang_id": 1,
        "jumlah": 1,
        "harga_jual_satuan": 76000 // opsional, jika kosong mengambil harga_jual di master
      },
      {
        "barang_id": 4,
        "jumlah": 2,
        "harga_jual_satuan": 4000
      }
    ]
  }
  ```
- **Response 201 Created:**
  ```json
  {
    "status": true,
    "message": "Transaksi penjualan berhasil disimpan dan stok otomatis dipotong.",
    "data": {
      "id": 1,
      "no_nota": "PJ-20260924-0001",
      "tanggal": "2026-09-24T13:10:00.000000Z",
      "kasir_id": 2,
      "total_belanja": 84000,
      "jumlah_bayar": 100000,
      "kembalian": 16000,
      "metode_pembayaran": "tunai",
      "kasir": {
        "id": 2,
        "name": "Siti Aminah (Kasir)"
      },
      "details": [
        {
          "id": 1,
          "barang_id": 1,
          "jumlah": 1,
          "harga_jual_satuan": 76000,
          "subtotal": 76000,
          "barang": {
            "id": 1,
            "nama_barang": "Beras Pandan Wangi 5kg"
          }
        },
        {
          "id": 2,
          "barang_id": 4,
          "jumlah": 2,
          "harga_jual_satuan": 4000,
          "subtotal": 8000,
          "barang": {
            "id": 4,
            "nama_barang": "Teh Botol Sosro Kotak 250ml"
          }
        }
      ]
    }
  }
  ```

#### [GET] `/api/penjualan/{id}/cetak-struk`
Menghasilkan dokumen struk belanja berformat **PDF** yang siap di-stream atau di-download langsung oleh Flutter.
- **Ukuran Kertas:** Disesuaikan untuk thermal roll printer kasir (Lebar 80mm / 58mm, tinggi proporsional dengan jumlah item).
- **Header Response:**
  ```http
  Content-Type: application/pdf
  Content-Disposition: inline; filename="struk_PJ-20260924-0001.pdf"
  ```

---

### 5.5. Laporan & Dasbor Keuangan (Owner)

#### [GET] `/api/laporan/dasbor` *(Pemilik)*
Menyajikan ringkasan kinerja toko secara cepat untuk halaman utama dasbor pemilik toko.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Data ringkasan dasbor berhasil dimuat.",
    "data": {
      "hari_ini": {
        "tanggal": "2026-09-24",
        "total_omset": 84000,
        "total_transaksi": 1
      },
      "bulan_ini": {
        "bulan": "September 2026",
        "total_omset": 84000,
        "total_transaksi": 1,
        "total_keuntungan_margin": 10000
      },
      "inventaris": {
        "total_produk": 5,
        "total_stok_menipis": 1,
        "daftar_stok_menipis": [
          {
            "id": 4,
            "kode_sku": "BRG-0004",
            "nama_barang": "Teh Botol Sosro Kotak 250ml",
            "stok": 2,
            "satuan": "pcs"
          }
        ]
      }
    }
  }
  ```

#### [GET] `/api/laporan/laba-rugi` *(Pemilik)*
Menghasilkan laporan laba rugi dengan membandingkan Omset Penjualan, HPP (Harga Pokok Penjualan), dan Pembelian Barang Masuk.
- **Query Parameters:**
  - `start_date` (opsional, format: `YYYY-MM-DD`, default: tanggal 1 bulan berjalan).
  - `end_date` (opsional, format: `YYYY-MM-DD`, default: akhir bulan berjalan).
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Laporan laba rugi berhasil diambil.",
    "data": {
      "periode": {
        "start_date": "2026-09-01",
        "end_date": "2026-09-30"
      },
      "pendapatan_penjualan": {
        "total_penjualan": 84000,
        "metode_tunai": 84000,
        "metode_qris": 0
      },
      "pengeluaran_dan_hpp": {
        "total_belanja_supplier": 1810000,
        "hpp_barang_terjual": 74000
      },
      "laba_rugi": {
        "laba_kotor_penjualan": 10000,
        "arus_kas_operasional": -1726000
      }
    }
  }
  ```

---

## 6. Contoh Implementasi di Flutter (Dart)

### 1. HTTP Service Client (Dio / http)
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  // Ganti IP dengan IP komputer server Anda
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;

  void setToken(String token) {
    _authToken = token;
  }

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (_authToken != null) 'Authorization': 'Bearer $_authToken',
  };

  // Login
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'Flutter Mobile',
      }),
    );

    final data = jsonDecode(response.body);
    if (response.statusCode == 200 && data['status'] == true) {
      setToken(data['data']['token']);
    }
    return data;
  }

  // Transaksi Kasir POS
  Future<Map<String, dynamic>> checkoutPenjualan({
    required String metodeBayar,
    required double jumlahBayar,
    required List<Map<String, dynamic>> items,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/penjualan'),
      headers: _headers,
      body: jsonEncode({
        'metode_pembayaran': metodeBayar,
        'jumlah_bayar': jumlahBayar,
        'items': items,
      }),
    );

    return jsonDecode(response.body);
  }
}
```

### 2. Menampilkan & Mencetak PDF Struk di Flutter
Untuk mendownload atau mencetak struk kasir di Flutter, gunakan paket `open_filex`, `flutter_pdfview`, atau kirim langsung ke printer thermal bluetooth:
```dart
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';

Future<File> downloadStrukPdf(int penjualanId, String token) async {
  final url = Uri.parse('http://10.0.2.2:8000/api/penjualan/$penjualanId/cetak-struk');
  
  final response = await http.get(
    url,
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/pdf',
    },
  );

  final dir = await getTemporaryDirectory();
  final file = File('${dir.path}/struk_$penjualanId.pdf');
  await file.writeAsBytes(response.bodyBytes);
  return file;
}
```

---

## 7. Pemecahan Masalah (Troubleshooting)

1. **Error: `Connection refused` pada Flutter:**
   - Jika menggunakan emulator Android, gunakan host `10.0.2.2` bukan `localhost`.
   - Pastikan server dijalankan dengan:
     ```bash
     php artisan serve --host=0.0.0.0 --port=8000
     ```
   - Pastikan firewall Windows mengizinkan port `8000`.

2. **Error 403 Forbidden:**
   - Terjadi saat user dengan peran `kasir` mencoba mengakses route yang dikhususkan untuk `pemilik` (seperti belanja, laporan laba rugi, atau CRUD master data). Login menggunakan akun pemilik.

3. **Error 422 Unprocessable Entity:**
   - Periksa key `errors` pada respon JSON untuk melihat field validasi yang tidak sesuai (misal: stok tidak mencukupi, uang bayar kurang, atau SKU duplikat).
