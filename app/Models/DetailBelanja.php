<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBelanja extends Model
{
    use HasFactory;

    protected $table = 'detail_belanja';

    protected $fillable = [
        'belanja_id',
        'barang_id',
        'jumlah',
        'harga_beli_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_beli_satuan' => 'float',
            'subtotal' => 'float',
        ];
    }

    public function belanja(): BelongsTo
    {
        return $this->belongsTo(Belanja::class, 'belanja_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
