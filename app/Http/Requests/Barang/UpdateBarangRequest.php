<?php

namespace App\Http\Requests\Barang;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $barangId = $this->route('barang')?->id ?? $this->route('barang') ?? $this->route('id');

        return [
            'kode_sku' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('barang', 'kode_sku')->ignore($barangId),
            ],
            'barcode' => ['nullable', 'string', 'max:100'],
            'nama_barang' => ['sometimes', 'required', 'string', 'max:150'],
            'kategori_id' => ['sometimes', 'required', 'integer', 'exists:kategori,id'],
            'harga_beli' => ['sometimes', 'required', 'numeric', 'min:0'],
            'harga_jual' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'satuan' => ['sometimes', 'required', 'string', 'max:20'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'gambar_url' => ['nullable', 'string', 'url'],
        ];
    }
}
