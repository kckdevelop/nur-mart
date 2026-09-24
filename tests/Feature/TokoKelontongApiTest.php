<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokoKelontongApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $pemilik;
    protected User $kasir;
    protected Kategori $kategori;
    protected Supplier $supplier;
    protected Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create([
            'name' => 'Pak Haji (Pemilik)',
            'email' => 'pemilik@test.com',
            'password' => bcrypt('password123'),
            'role' => 'pemilik',
        ]);

        $this->kasir = User::factory()->create([
            'name' => 'Mbak Kasir',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password123'),
            'role' => 'kasir',
        ]);

        $this->kategori = Kategori::create([
            'nama_kategori' => 'Sembako',
        ]);

        $this->supplier = Supplier::create([
            'nama_supplier' => 'PT Sumber Rejeki',
            'no_telepon' => '08123456789',
            'alamat' => 'Pasar Induk',
        ]);

        $this->barang = Barang::create([
            'kode_sku' => 'SKU-001',
            'barcode' => '899123456',
            'nama_barang' => 'Minyak Goreng 1L',
            'kategori_id' => $this->kategori->id,
            'harga_beli' => 14000,
            'harga_jual' => 16500,
            'stok' => 20,
            'satuan' => 'pcs',
        ]);
    }

    public function test_login_and_get_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'kasir@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ]
            ]);
    }

    public function test_transaksi_belanja_increases_stock(): void
    {
        $initialStock = $this->barang->stok;
        $tambahStok = 10;

        $response = $this->actingAs($this->pemilik, 'sanctum')->postJson('/api/belanja', [
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->format('Y-m-d'),
            'items' => [
                [
                    'barang_id' => $this->barang->id,
                    'jumlah' => $tambahStok,
                    'harga_beli_satuan' => 14500,
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', true);

        $this->barang->refresh();
        $this->assertEquals($initialStock + $tambahStok, $this->barang->stok);
        $this->assertEquals(14500, $this->barang->harga_beli);
    }

    public function test_transaksi_penjualan_decreases_stock(): void
    {
        $initialStock = $this->barang->stok;
        $qtyJual = 3;

        $response = $this->actingAs($this->kasir, 'sanctum')->postJson('/api/penjualan', [
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 50000,
            'items' => [
                [
                    'barang_id' => $this->barang->id,
                    'jumlah' => $qtyJual,
                    'harga_jual_satuan' => 16500,
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', true);

        $this->barang->refresh();
        $this->assertEquals($initialStock - $qtyJual, $this->barang->stok);
    }

    public function test_penjualan_fails_when_stock_insufficient(): void
    {
        $response = $this->actingAs($this->kasir, 'sanctum')->postJson('/api/penjualan', [
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 1000000,
            'items' => [
                [
                    'barang_id' => $this->barang->id,
                    'jumlah' => 999, // Lebih besar dari stok 20
                    'harga_jual_satuan' => 16500,
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    public function test_kasir_forbidden_to_access_laporan_dasbor(): void
    {
        $response = $this->actingAs($this->kasir, 'sanctum')->getJson('/api/laporan/dasbor');

        $response->assertStatus(403)
            ->assertJsonPath('status', false);
    }

    public function test_pemilik_can_access_laporan_dasbor(): void
    {
        $response = $this->actingAs($this->pemilik, 'sanctum')->getJson('/api/laporan/dasbor');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'hari_ini',
                    'bulan_ini',
                    'inventaris',
                ]
            ]);
    }
}
