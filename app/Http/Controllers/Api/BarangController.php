<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barang\StoreBarangRequest;
use App\Http\Requests\Barang\UpdateBarangRequest;
use App\Models\Barang;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Barang::with('kategori');

        // Filter pencarian nama / sku / barcode
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'nama_barang');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Opsi pagination untuk Flutter
        if ($request->boolean('all', false)) {
            $barangs = $query->get();
            return $this->successResponse($barangs, 'Daftar semua produk berhasil diambil.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $barangs = $query->paginate($perPage);

        return $this->successResponse($barangs, 'Daftar produk berhasil diambil.');
    }

    public function store(StoreBarangRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle upload gambar — simpan path relatif ke DB
        // accessor gambar_full_url di model yang konversi ke full URL
        if ($request->hasFile('gambar')) {
            $data['gambar_url'] = $request->file('gambar')->store('barangs', 'public');
        }

        unset($data['gambar']);

        $barang = Barang::create($data);
        $barang->load('kategori');

        return $this->successResponse($barang, 'Produk berhasil ditambahkan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $barang = Barang::with('kategori')->find($id);

        if (!$barang) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        return $this->successResponse($barang, 'Detail produk berhasil diambil.');
    }

    public function update(UpdateBarangRequest $request, int $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        $data = $request->validated();

        // Ganti gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            $barang->hapusGambar(); // hapus file lama dari storage
            $data['gambar_url'] = $request->file('gambar')->store('barangs', 'public');
        }

        unset($data['gambar']);

        $barang->update($data);
        $barang->load('kategori');

        return $this->successResponse($barang, 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $barang = Barang::withCount(['detailPenjualans', 'detailBelanjas'])->find($id);

        if (!$barang) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        if ($barang->detail_penjualans_count > 0 || $barang->detail_belanjas_count > 0) {
            return $this->errorResponse(
                'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan/pembelian.',
                422
            );
        }

        $barang->hapusGambar(); // hapus file gambar dari storage
        $barang->delete();

        return $this->successResponse(null, 'Produk berhasil dihapus.');
    }

    /**
     * Hapus hanya gambar produk tanpa menghapus produk itu sendiri.
     * DELETE /api/barang/{id}/gambar
     */
    public function destroyGambar(int $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        if (empty($barang->gambar_url)) {
            return $this->errorResponse('Produk ini belum memiliki gambar.', 422);
        }

        $barang->hapusGambar();
        $barang->update(['gambar_url' => null]);

        return $this->successResponse(null, 'Gambar produk berhasil dihapus.');
    }

    /**
     * Endpoint khusus cek produk dengan stok menipis
     */
    public function stokMenipis(Request $request): JsonResponse
    {
        $threshold = (int) $request->get('threshold', 10);
        $barangs = Barang::with('kategori')
            ->stokMenipis($threshold)
            ->orderBy('stok', 'asc')
            ->get();

        return $this->successResponse([
            'threshold' => $threshold,
            'total_items' => $barangs->count(),
            'items' => $barangs,
        ], 'Daftar stok barang menipis berhasil diambil.');
    }
}
