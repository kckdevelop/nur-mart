<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pemilik
        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik@nurmart.com'],
            [
                'name' => 'Haji Mansyur (Pemilik Toko)',
                'password' => Hash::make('password123'),
                'role' => 'pemilik',
            ]
        );

        // 2. Akun Kasir
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@nurmart.com'],
            [
                'name' => 'Siti Aminah (Kasir)',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );

        // 3. Kategori
        $katSembako = Kategori::firstOrCreate(['nama_kategori' => 'Sembako']);
        $katMinuman = Kategori::firstOrCreate(['nama_kategori' => 'Minuman']);
        $katSnack   = Kategori::firstOrCreate(['nama_kategori' => 'Makanan Ringan']);
        $katSabun   = Kategori::firstOrCreate(['nama_kategori' => 'Kebutuhan Mandi & Cuci']);

        // 4. Supplier
        $sup1 = Supplier::firstOrCreate(
            ['nama_supplier' => 'PT Sumber Pangan Sejahtera'],
            [
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Pergudangan Indah No. 12, Jakarta Timur'
            ]
        );

        $sup2 = Supplier::firstOrCreate(
            ['nama_supplier' => 'CV Distribusi Berkah Jaya'],
            [
                'no_telepon' => '082198765432',
                'alamat' => 'Jl. Pasar Baru No. 45, Bekasi'
            ]
        );

        // 5. Barang / Produk
        Barang::firstOrCreate(
            ['kode_sku' => 'BRG-0001'],
            [
                'barcode' => '8992345100012',
                'nama_barang' => 'Beras Pandan Wangi 5kg',
                'kategori_id' => $katSembako->id,
                'harga_beli' => 68000,
                'harga_jual' => 76000,
                'stok' => 25,
                'satuan' => 'dus/karung',
                'gambar_url' => null,
            ]
        );

        Barang::firstOrCreate(
            ['kode_sku' => 'BRG-0002'],
            [
                'barcode' => '8999999555111',
                'nama_barang' => 'Minyak Goreng SunCo 2 Liter',
                'kategori_id' => $katSembako->id,
                'harga_beli' => 33000,
                'harga_jual' => 37500,
                'stok' => 40,
                'satuan' => 'pcs',
                'gambar_url' => null,
            ]
        );

        Barang::firstOrCreate(
            ['kode_sku' => 'BRG-0003'],
            [
                'barcode' => '8991102220033',
                'nama_barang' => 'Gula Pasir Gulaku 1kg',
                'kategori_id' => $katSembako->id,
                'harga_beli' => 15000,
                'harga_jual' => 17500,
                'stok' => 50,
                'satuan' => 'pcs',
                'gambar_url' => null,
            ]
        );

        Barang::firstOrCreate(
            ['kode_sku' => 'BRG-0004'],
            [
                'barcode' => '8998866200044',
                'nama_barang' => 'Teh Botol Sosro Kotak 250ml',
                'kategori_id' => $katMinuman->id,
                'harga_beli' => 3000,
                'harga_jual' => 4000,
                'stok' => 4, // Sengaja di bawah 10 untuk tes peringatan stok menipis
                'satuan' => 'pcs',
                'gambar_url' => null,
            ]
        );

        Barang::firstOrCreate(
            ['kode_sku' => 'BRG-0005'],
            [
                'barcode' => '8993456789012',
                'nama_barang' => 'Indomie Goreng Original 85g',
                'kategori_id' => $katSnack->id,
                'harga_beli' => 2800,
                'harga_jual' => 3500,
                'stok' => 120,
                'satuan' => 'pcs',
                'gambar_url' => null,
            ]
        );
    }
}
