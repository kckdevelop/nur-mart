<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'no_telepon',
        'alamat',
    ];

    public function belanjas(): HasMany
    {
        return $this->hasMany(Belanja::class, 'supplier_id');
    }
}
