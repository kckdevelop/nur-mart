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
Kecuali endpoint `/login` dan `/penjualan/{id}/cetak-struk`, seluruh request wajib menyertakan header berikut:
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
| **`pemilik`** | Pemilik Toko (Owner) | **Akses Penuh:** Dashboard & Laporan Keuangan, Pembelian Belanja (Kulakan), CRUD Master Data (Barang, Kategori, Supplier), Manajemen Pengguna/Kasir, dan Pengaturan Toko. |
| **`kasir`** | Operator Kasir (Staff) | **Hanya POS:** Transaksi Penjualan POS, Cetak Struk Belanja, Riwayat Penjualan, dan Baca Master Data Produk/Kategori (Read-Only untuk kasir). Tidak memiliki akses ke data Supplier, Belanja, Manajemen User, Pengaturan Toko, maupun Laporan. |

> [!IMPORTANT]
> **Kebijakan Pembatasan Akses Kasir (Update 24 Sep 2026):**
> 1. Pada antarmuka Web Dashboard (`/`), akun dengan role **kasir** hanya dapat melihat dan mengakses **Tab Kasir POS**. Seluruh tombol navigasi admin, manajemen data, dan laporan disembunyikan dan diblokir.
> 2. Pada REST API, endpoint **Supplier** (`/api/supplier`) kini sepenuhnya dipindahkan ke grup **Pemilik Saja** (`role:pemilik`), sehingga kasir tidak dapat mengakses daftar supplier maupun data kulakan.

### Akun Uji Coba Bawaan (Default Seeder)

| Email | Password | Role | Keterangan |
| :--- | :--- | :--- | :--- |
| `pemilik@nurmart.com` | `password123` | `pemilik` | Akun Owner untuk manajemen toko & laporan |
| `kasir@nurmart.com` | `password123` | `kasir` | Akun Kasir untuk operasional POS harian |

---

## 4. Matriks Akses Endpoint

Tabel berikut menunjukkan hak akses untuk masing-masing peran pengguna:

| Modul | Endpoint | Method | Kasir | Pemilik | Keterangan |
| :--- | :--- | :--- | :---: | :---: | :--- |
| **Auth & Profil** | `/api/login` | `POST` | ✅ | ✅ | Publik / Tanpa token |
| | `/api/profile` | `GET` | ✅ | ✅ | Info profil akun aktif |
| | `/api/profile` | `PUT` | ✅ | ✅ | Update nama, telp, alamat, password |
| | `/api/refresh-token` | `POST` | ✅ | ✅ | Perbarui token Sanctum |
| | `/api/logout` | `POST` | ✅ | ✅ | Hapus sesi token aktif |
| **Pengaturan Toko** | `/api/pengaturan` | `GET` | ✅ | ✅ | Read-only (Info toko, kontak, struk) |
| | `/api/pengaturan` | `PUT` | ❌ | 🔒 | Update info toko & footer struk |
| **Manajemen User** | `/api/users` | `GET` | ❌ | 🔒 | Daftar seluruh akun kasir & pemilik |
| | `/api/users` | `POST` | ❌ | 🔒 | Tambah akun kasir / user baru |
| | `/api/users/{id}` | `GET` | ❌ | 🔒 | Detail akun user |
| | `/api/users/{id}` | `PUT` | ❌ | 🔒 | Edit data / password akun user |
| | `/api/users/{id}` | `DELETE` | ❌ | 🔒 | Hapus akun user |
| **Kategori** | `/api/kategori` | `GET` | ✅ | ✅ | Filter kategori di POS kasir |
| | `/api/kategori/{id}` | `GET` | ✅ | ✅ | Detail kategori |
| | `/api/kategori` | `POST` | ❌ | 🔒 | Tambah kategori baru |
| | `/api/kategori/{id}` | `PUT` | ❌ | 🔒 | Ubah nama kategori |
| | `/api/kategori/{id}` | `DELETE` | ❌ | 🔒 | Hapus kategori |
| **Supplier** | `/api/supplier` | `GET` | ❌ | 🔒 | *(Pemilik Saja per 24 Sep 2026)* |
| | `/api/supplier/{id}` | `GET` | ❌ | 🔒 | *(Pemilik Saja per 24 Sep 2026)* |
| | `/api/supplier` | `POST` | ❌ | 🔒 | Tambah supplier baru |
| | `/api/supplier/{id}` | `PUT` | ❌ | 🔒 | Ubah data supplier |
| | `/api/supplier/{id}` | `DELETE` | ❌ | 🔒 | Hapus data supplier |
| **Master Barang** | `/api/barang` | `GET` | ✅ | ✅ | Katalog produk & pencarian POS |
| | `/api/barang/stok-menipis`| `GET` | ✅ | ✅ | Peringatan stok kritis |
| | `/api/barang/{id}` | `GET` | ✅ | ✅ | Detail barang |
| | `/api/barang` | `POST` | ❌ | 🔒 | Tambah produk & upload gambar |
| | `/api/barang/{id}` | `POST` / `PUT`| ❌ | 🔒 | Update produk & ganti gambar |
| | `/api/barang/{id}/gambar` | `DELETE` | ❌ | 🔒 | Hapus file gambar produk |
| | `/api/barang/{id}` | `DELETE` | ❌ | 🔒 | Hapus data produk |
| **Purchasing** | `/api/belanja` | `GET` | ❌ | 🔒 | Riwayat kulakan / pembelian |
| | `/api/belanja` | `POST` | ❌ | 🔒 | Catat belanja (stok otomatis bertambah) |
| | `/api/belanja/{id}` | `GET` | ❌ | 🔒 | Detail faktur belanja |
| **Sales POS** | `/api/penjualan` | `GET` | ✅ | ✅ | Riwayat transaksi penjualan |
| | `/api/penjualan` | `POST` | ✅ | ✅ | Checkout POS (stok otomatis berkurang) |
| | `/api/penjualan/{id}` | `GET` | ✅ | ✅ | Detail transaksi nota |
| | `/api/penjualan/{id}/cetak-struk` | `GET` | ✅ | ✅ | Download / cetak PDF thermal |
| **Laporan** | `/api/laporan/dasbor` | `GET` | ❌ | 🔒 | Ringkasan omset & statistik |
| | `/api/laporan/laba-rugi` | `GET` | ❌ | 🔒 | Perhitungan laba kotor & arus kas |

*Keterangan: ✅ = Diizinkan | ❌ = Akses Ditolak (HTTP 403) | 🔒 = Dikhususkan untuk Pemilik*

---

## 5. Rincian API Endpoint

### 5.1. Autentikasi & Profil Pengguna

#### [POST] `/api/login`
Mengautentikasi akun dan mendapatkan token akses Bearer Sanctum.
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
        "role": "kasir",
        "telepon": "08123456789",
        "alamat": "Dusun Sukasari RT 01"
      },
      "token": "1|8A7g89...dF6h9",
      "token_type": "Bearer"
    }
  }
  ```

#### [GET] `/api/profile`
Mendapatkan informasi profil akun yang sedang login.
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
      "telepon": "08123456789",
      "alamat": "Dusun Sukasari RT 01",
      "created_at": "2026-09-24T06:05:00.000000Z"
    }
  }
  ```

#### [PUT] `/api/profile` *(Kasir & Pemilik)*
Mengubah profil akun sendiri (Nama, Email, Telepon, Alamat, dan ganti Password opsional).
- **Request Body:**
  ```json
  {
    "name": "Siti Aminah",
    "email": "kasir@nurmart.com",
    "telepon": "0812-9876-5432",
    "alamat": "Dusun Sukasari RT 01",
    "password_lama": "password123",
    "password_baru": "passwordBaru456"
  }
  ```
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Profil berhasil diperbarui.",
    "data": {
      "id": 2,
      "name": "Siti Aminah",
      "email": "kasir@nurmart.com",
      "role": "kasir",
      "telepon": "0812-9876-5432",
      "alamat": "Dusun Sukasari RT 01"
    }
  }
  ```

#### [POST] `/api/refresh-token`
Memperbarui token yang sedang aktif dan mencabut token lama.
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
Mencabut token sesi yang sedang aktif.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Logout berhasil, sesi telah dihapus.",
    "data": null
  }
  ```

---

### 5.2. Pengaturan Toko & Kontak Struk

#### [GET] `/api/pengaturan` *(Kasir & Pemilik)*
Mengambil informasi toko, kontak, alamat, dan footer nota cetak struk.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Data pengaturan toko berhasil diambil.",
    "data": {
      "id": 1,
      "nama_toko": "TOKO KELONTONG NURMART",
      "slogan": "Sedia Sembako & Kebutuhan Rumah Tangga Terlengkap",
      "no_telepon": "0812-3456-7890",
      "alamat": "Jogodayoh RT 02, Sedia Sembako & Kebutuhan Rumah Tangga",
      "footer_struk": "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan. Terima kasih atas kunjungan Anda!",
      "created_at": "2026-09-24T09:09:52.000000Z",
      "updated_at": "2026-09-24T09:09:52.000000Z"
    }
  }
  ```

#### [PUT] `/api/pengaturan` *(Hanya Pemilik)*
Memperbarui informasi toko, alamat, kontak, atau footer nota thermal struk.
- **Request Body:**
  ```json
  {
    "nama_toko": "TOKO KELONTONG NURMART",
    "slogan": "Pusat Grosir & Eceran Terlengkap",
    "no_telepon": "0812-3456-7890",
    "alamat": "Jl. Raya Jogodayoh RT 02 RW 01, Majalengka",
    "footer_struk": "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan. Terima kasih!"
  }
  ```
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Pengaturan toko berhasil diperbarui.",
    "data": { ... }
  }
  ```

---

### 5.3. Manajemen User & Akun Kasir (Pemilik)

#### [GET] `/api/users` *(Hanya Pemilik)*
Mengambil daftar seluruh pengguna dan kasir yang terdaftar.
- **Query Parameters (Opsional):**
  - `role`: Filter `pemilik` atau `kasir`
  - `q`: Pencarian nama, email, atau nomor telepon
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Daftar user berhasil diambil.",
    "data": [
      {
        "id": 1,
        "name": "H. Ahmad Nur (Pemilik)",
        "email": "pemilik@nurmart.com",
        "role": "pemilik",
        "telepon": "081234567890",
        "alamat": "Majalengka",
        "created_at": "2026-09-24T06:05:00.000000Z"
      },
      {
        "id": 2,
        "name": "Siti Aminah (Kasir)",
        "email": "kasir@nurmart.com",
        "role": "kasir",
        "telepon": "081298765432",
        "alamat": "Dusun Sukasari RT 01",
        "created_at": "2026-09-24T06:05:00.000000Z"
      }
    ]
  }
  ```

#### [POST] `/api/users` *(Hanya Pemilik)*
Menambahkan akun pengguna atau kasir baru.
- **Request Body:**
  ```json
  {
    "name": "Ahmad Dani",
    "email": "dani@nurmart.com",
    "password": "password123",
    "role": "kasir",
    "telepon": "085712345678",
    "alamat": "Desa Maju RT 03"
  }
  ```

#### [GET] `/api/users/{id}` *(Hanya Pemilik)*
Mengambil detail satu pengguna.

#### [PUT] `/api/users/{id}` *(Hanya Pemilik)*
Memperbarui data atau password akun user/kasir.

#### [DELETE] `/api/users/{id}` *(Hanya Pemilik)*
Menghapus akun pengguna. *(Catatan: Akun yang sedang login aktif atau memiliki relasi transaksi tidak dapat dihapus)*.

---

### 5.4. Kategori Produk

#### [GET] `/api/kategori` *(Kasir & Pemilik)*
Mengambil daftar semua kategori produk. Digunakan oleh POS untuk tab/filter kategori barang.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Daftar kategori berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_kategori": "Sembako",
        "deskripsi": "Beras, Minyak, Gula, Tepung"
      },
      {
        "id": 2,
        "nama_kategori": "Minuman",
        "deskripsi": "Air mineral, teh, kopi, jus"
      }
    ]
  }
  ```

#### [POST] `/api/kategori` *(Hanya Pemilik)*
Menambah kategori baru.
- **Request Body:** `{"nama_kategori": "Snack & Biskuit", "deskripsi": "Aneka makanan ringan"}`

#### [PUT] `/api/kategori/{id}` *(Hanya Pemilik)*
Mengubah nama/deskripsi kategori.

#### [DELETE] `/api/kategori/{id}` *(Hanya Pemilik)*
Menghapus kategori (Hanya kategori tanpa relasi produk yang dapat dihapus).

---

### 5.5. Supplier (Khusus Pemilik)

> [!WARNING]
> Seluruh endpoint Supplier kini dibatasi **Hanya Pemilik** (`role:pemilik`). Akun kasir yang mencoba mengakses endpoint ini akan menerima respon **HTTP 403 Forbidden**.

#### [GET] `/api/supplier` *(Hanya Pemilik)*
Mengambil daftar supplier untuk keperluan kulakan / pembelian barang.
- **Response 200 OK:**
  ```json
  {
    "status": true,
    "message": "Daftar supplier berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_supplier": "PT Sumber Pangan Sejahtera",
        "kontak_person": "Bpk. Hendra",
        "telepon": "0812-3456-7890",
        "alamat": "Kawasan Industri Cirebon"
      }
    ]
  }
  ```

#### [POST] `/api/supplier` *(Hanya Pemilik)*
Menambah data supplier baru.
- **Request Body:**
  ```json
  {
    "nama_supplier": "CV Maju Jaya Abadi",
    "kontak_person": "Ibu Ratna",
    "telepon": "0813-8899-7766",
    "alamat": "Jl. Industri No. 45, Majalengka"
  }
  ```

#### [PUT] `/api/supplier/{id}` *(Hanya Pemilik)*
Mengubah data supplier.

#### [DELETE] `/api/supplier/{id}` *(Hanya Pemilik)*
Menghapus data supplier.

---

### 5.6. Master Data Barang / Produk

#### [GET] `/api/barang` *(Kasir & Pemilik)*
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
          "gambar_url": "http://localhost:8000/storage/produk/beras.webp",
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

#### [GET] `/api/barang/stok-menipis` *(Kasir & Pemilik)*
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

#### [POST] `/api/barang` *(Hanya Pemilik)*
Menambah produk baru. Mendukung upload gambar file (`multipart/form-data`).
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

#### [POST / PUT] `/api/barang/{id}` *(Hanya Pemilik)*
Memperbarui data produk. Jika mengunggah file gambar baru melalui Flutter, gunakan method `POST` dengan `multipart/form-data`.

#### [DELETE] `/api/barang/{id}/gambar` *(Hanya Pemilik)*
Menghapus file foto/gambar dari produk tanpa menghapus data barang.

#### [DELETE] `/api/barang/{id}` *(Hanya Pemilik)*
Menghapus produk dari database (beserta file gambarnya dari storage).

---

### 5.7. Belanja Barang / Purchasing (Khusus Pemilik)

Fitur ini digunakan saat pemilik toko membeli pasokan/kulakan dari supplier.
Sistem secara otomatis **menambahkan stok barang** dan **mengupdate harga beli terbaru** pada database dalam satu transaksi aman (`DB::transaction`).

#### [GET] `/api/belanja` *(Hanya Pemilik)*
Mengambil riwayat transaksi belanja/kulakan ke supplier.

#### [POST] `/api/belanja` *(Hanya Pemilik)*
- **Request Body:**
  ```json
  {
    "supplier_id": 1,
    "no_faktur_pembelian": "INV-SUP-2026-009",
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

### 5.8. POS / Kasir & Penjualan (Sales)

Fitur ini digunakan oleh kasir dan pemilik untuk melayani transaksi penjualan kasir di toko.

#### [GET] `/api/penjualan` *(Kasir & Pemilik)*
Mengambil riwayat transaksi penjualan kasir (mendukung filter tanggal dan pagination).

#### [POST] `/api/penjualan` *(Kasir & Pemilik)*
- **Fitur Otomatis:**
  1. Validasi kecukupan stok secara atomik (`lockForUpdate`).
  2. Jika stok tidak mencukupi, transaksi ditolak dan mengembalikan HTTP 422 beserta nama produk yang kurang.
  3. Memotong stok barang secara otomatis.
  4. Menghitung kembalian berdasarkan `jumlah_bayar - total_belanja`.

- **Request Body:**
  ```json
  {
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 100000,
    "items": [
      {
        "barang_id": 1,
        "jumlah": 1,
        "harga_jual_satuan": 76000
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

#### [GET] `/api/penjualan/{id}/cetak-struk` *(Publik / Kasir / Pemilik)*
Menghasilkan dokumen struk belanja berformat **PDF** yang siap di-stream atau di-download langsung oleh Flutter / printer thermal.
- **Ukuran Kertas:** Disesuaikan untuk printer thermal roll kasir (Lebar 80mm / 58mm).
- **Header Response:**
  ```http
  Content-Type: application/pdf
  Content-Disposition: inline; filename="struk_PJ-20260924-0001.pdf"
  ```

---

### 5.9. Laporan & Dasbor Keuangan (Khusus Pemilik)

#### [GET] `/api/laporan/dasbor` *(Hanya Pemilik)*
Menyajikan ringkasan kinerja toko secara cepat untuk dasbor pemilik toko.
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

#### [GET] `/api/laporan/laba-rugi` *(Hanya Pemilik)*
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
  // Ganti URL sesuai lingkungan (10.0.2.2 untuk Android Emulator)
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;
  String? _userRole;

  void setAuth(String token, String role) {
    _authToken = token;
    _userRole = role;
  }

  bool get isKasir => _userRole == 'kasir';
  bool get isPemilik => _userRole == 'pemilik';

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (_authToken != null) 'Authorization': 'Bearer $_authToken',
  };

  // 1. Login
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'Flutter Mobile App',
      }),
    );

    final data = jsonDecode(response.body);
    if (response.statusCode == 200 && data['status'] == true) {
      setAuth(data['data']['token'], data['data']['user']['role']);
    }
    return data;
  }

  // 2. Update Profil Pengguna
  Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    String? telepon,
    String? alamat,
    String? passwordLama,
    String? passwordBaru,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/profile'),
      headers: _headers,
      body: jsonEncode({
        'name': name,
        'email': email,
        if (telepon != null) 'telepon': telepon,
        if (alamat != null) 'alamat': alamat,
        if (passwordLama != null) 'password_lama': passwordLama,
        if (passwordBaru != null) 'password_baru': passwordBaru,
      }),
    );
    return jsonDecode(response.body);
  }

  // 3. Transaksi Kasir POS (Tersedia untuk Kasir & Pemilik)
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
Untuk mendownload atau mencetak struk kasir di Flutter, gunakan paket `path_provider` dan kirim ke printer thermal bluetooth:
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

## 7. Changelog Pembaruan Hak Akses & Fitur

| Versi / Tanggal | Modul | Perubahan |
| :--- | :--- | :--- |
| **24 Sep 2026** | **Web Dashboard** | Hak akses role `kasir` dibatasi hanya melihat tab **Kasir POS**. Tab Navigasi Dasbor, Master Data, Belanja, dan Laporan disembunyikan. |
| **24 Sep 2026** | **API Supplier** | Endpoint `GET /api/supplier` & `GET /api/supplier/{id}` dipindahkan dari grup kasir ke **Hanya Pemilik** (`role:pemilik`). |
| **24 Sep 2026** | **API Profil & Toko** | Penambahan endpoint `PUT /api/profile` (telepon, alamat, password) dan `GET|PUT /api/pengaturan` (pengaturan toko). |
| **24 Sep 2026** | **API User** | Penambahan endpoint CRUD `/api/users` khusus pemilik untuk manajemen akun kasir. |
| **24 Sep 2026** | **API Barang** | Penambahan endpoint `DELETE /api/barang/{id}/gambar` untuk menghapus foto produk saja. |

---

## 8. Pemecahan Masalah (Troubleshooting)

1. **Error: `Connection refused` pada Flutter:**
   - Jika menggunakan emulator Android, gunakan host `10.0.2.2` bukan `localhost`.
   - Pastikan server dijalankan dengan:
     ```bash
     php artisan serve --host=0.0.0.0 --port=8000
     ```
   - Pastikan firewall Windows mengizinkan port `8000`.

2. **Error 403 Forbidden (Akses Ditolak):**
   - Terjadi saat user dengan peran `kasir` mencoba mengakses route yang dikhususkan untuk `pemilik` (seperti supplier, belanja, laporan laba rugi, manajemen user, atau pengaturan toko). Login menggunakan akun pemilik.

3. **Kasir Mengalami Error saat Akses Supplier di Flutter:**
   - Sesuai kebijakan terbaru per 24 Sep 2026, akun kasir tidak diizinkan membaca data supplier. Pastikan aplikasi Flutter menyembunyikan menu supplier dari pengguna bertipe `kasir`.

4. **Error 422 Unprocessable Entity:**
   - Periksa key `errors` pada respon JSON untuk melihat field validasi yang tidak sesuai (misal: stok tidak mencukupi, uang bayar kurang, atau SKU duplikat).
