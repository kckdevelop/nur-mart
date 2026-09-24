<?php

namespace App\Http\Requests\Penjualan;

use App\Http\Requests\BaseApiRequest;

class StorePenjualanRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'tanggal' => ['nullable', 'date'],
            'metode_pembayaran' => ['required', 'string', 'in:tunai,qris'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_jual_satuan' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih (tunai atau qris).',
            'metode_pembayaran.in' => 'Metode pembayaran hanya diperbolehkan tunai atau qris.',
            'jumlah_bayar.required' => 'Jumlah uang yang dibayarkan pelanggan wajib diisi.',
            'items.required' => 'Daftar barang penjualan tidak boleh kosong.',
            'items.min' => 'Transaksi harus memiliki minimal 1 item barang.',
            'items.*.barang_id.required' => 'Barang ID wajib diisi.',
            'items.*.barang_id.exists' => 'Barang tidak ditemukan.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
        ];
    }
}
