<?php

namespace App\Http\Requests\Belanja;

use App\Http\Requests\BaseApiRequest;

class StoreBelanjaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:supplier,id'],
            'no_faktur_pembelian' => ['nullable', 'string', 'max:100', 'unique:belanja,no_faktur_pembelian'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli_satuan' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'tanggal.required' => 'Tanggal belanja wajib diisi.',
            'items.required' => 'Daftar barang belanjaan wajib diisi.',
            'items.min' => 'Minimal belanja harus memiliki 1 item barang.',
            'items.*.barang_id.required' => 'ID barang pada item wajib diisi.',
            'items.*.barang_id.exists' => 'Barang tidak ditemukan.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
            'items.*.harga_beli_satuan.required' => 'Harga beli satuan wajib diisi.',
        ];
    }
}
