<?php

namespace App\Http\Requests\Barang;

use App\Http\Requests\BaseApiRequest;

class StoreBarangRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'kode_sku' => ['required', 'string', 'max:50', 'unique:barang,kode_sku'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:20'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'gambar_url' => ['nullable', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_sku.required' => 'Kode SKU wajib diisi.',
            'kode_sku.unique' => 'Kode SKU sudah terdaftar.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'satuan.required' => 'Satuan barang (pcs/kg/dus/dll) wajib diisi.',
        ];
    }
}
