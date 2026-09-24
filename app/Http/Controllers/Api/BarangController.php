<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barang\StoreBarangRequest;
use App\Http\Requests\Barang\UpdateBarangRequest;
use App\Models\Barang;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        // Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('barangs', 'public');
            $data['gambar_url'] = url(Storage::url($path));
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

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada di local storage
            if ($barang->gambar_url) {
                $oldPath = str_replace(url('/storage') . '/', '', $barang->gambar_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('gambar')->store('barangs', 'public');
            $data['gambar_url'] = url(Storage::url($path));
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

        // Hapus file gambar jika ada
        if ($barang->gambar_url) {
            $path = str_replace(url('/storage') . '/', '', $barang->gambar_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $barang->delete();

        return $this->successResponse(null, 'Produk berhasil dihapus.');
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
