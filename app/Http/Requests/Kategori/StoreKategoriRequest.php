<?php

namespace App\Http\Requests\Kategori;

use App\Http\Requests\BaseApiRequest;

class StoreKategoriRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ];
    }
}
