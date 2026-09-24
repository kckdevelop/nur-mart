<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kategori\StoreKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use App\Models\Kategori;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Kategori::withCount('barangs');

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', "%{$request->search}%");
        }

        $kategoris = $query->orderBy('nama_kategori', 'asc')->get();

        return $this->successResponse($kategoris, 'Daftar kategori berhasil diambil.');
    }

    public function store(StoreKategoriRequest $request): JsonResponse
    {
        $kategori = Kategori::create($request->validated());

        return $this->successResponse($kategori, 'Kategori berhasil ditambahkan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $kategori = Kategori::withCount('barangs')->find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        return $this->successResponse($kategori, 'Detail kategori berhasil diambil.');
    }

    public function update(UpdateKategoriRequest $request, int $id): JsonResponse
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        $kategori->update($request->validated());

        return $this->successResponse($kategori, 'Kategori berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $kategori = Kategori::withCount('barangs')->find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        if ($kategori->barangs_count > 0) {
            return $this->errorResponse('Kategori tidak dapat dihapus karena masih memiliki barang terkait.', 422);
        }

        $kategori->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus.');
    }
}
