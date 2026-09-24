<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    use ApiResponseTrait;

    /**
     * Dapatkan informasi pengaturan toko (nama, kontak, alamat, footer struk)
     */
    public function index(): JsonResponse
    {
        $pengaturan = Pengaturan::getUtama();

        return $this->successResponse($pengaturan, 'Data pengaturan toko berhasil diambil.');
    }

    /**
     * Perbarui data pengaturan toko (Khusus role pemilik)
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:150',
            'slogan' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:40',
            'alamat' => 'nullable|string|max:500',
            'footer_struk' => 'nullable|string|max:500',
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi.',
            'nama_toko.max' => 'Nama toko maksimal 150 karakter.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $pengaturan = Pengaturan::getUtama();
        $pengaturan->update($validator->validated());

        return $this->successResponse($pengaturan, 'Pengaturan toko berhasil diperbarui.');
    }
}
