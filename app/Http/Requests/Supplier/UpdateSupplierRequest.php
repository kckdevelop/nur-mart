<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\BaseApiRequest;

class UpdateSupplierRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nama_supplier' => ['sometimes', 'required', 'string', 'max:150'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }
}
