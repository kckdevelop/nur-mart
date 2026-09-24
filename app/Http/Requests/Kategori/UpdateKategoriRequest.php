<?php

namespace App\Http\Requests\Kategori;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $kategoriId = $this->route('kategori')?->id ?? $this->route('kategori') ?? $this->route('id');

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama_kategori')->ignore($kategoriId),
            ],
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
