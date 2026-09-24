<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\BaseApiRequest;

class StoreSupplierRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nama_supplier' => ['required', 'string', 'max:150'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
        ];
    }
}
