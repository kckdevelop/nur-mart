<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
        }

        $suppliers = $query->orderBy('nama_supplier', 'asc')->get();

        return $this->successResponse($suppliers, 'Daftar supplier berhasil diambil.');
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        return $this->successResponse($supplier, 'Supplier berhasil ditambahkan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $supplier = Supplier::with('belanjas')->find($id);

        if (!$supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        return $this->successResponse($supplier, 'Detail supplier berhasil diambil.');
    }

    public function update(UpdateSupplierRequest $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        $supplier->update($request->validated());

        return $this->successResponse($supplier, 'Supplier berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $supplier = Supplier::withCount('belanjas')->find($id);

        if (!$supplier) {
            return $this->errorResponse('Supplier tidak ditemukan.', 404);
        }

        if ($supplier->belanjas_count > 0) {
            return $this->errorResponse('Supplier tidak dapat dihapus karena memiliki riwayat transaksi belanja.', 422);
        }

        $supplier->delete();

        return $this->successResponse(null, 'Supplier berhasil dihapus.');
    }
}
