<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_sku',
        'barcode',
        'nama_barang',
        'kategori_id',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'gambar_url',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'float',
            'harga_jual' => 'float',
            'stok' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailBelanjas(): HasMany
    {
        return $this->hasMany(DetailBelanja::class, 'barang_id');
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'barang_id');
    }

    /**
     * Scope for searching by keyword (sku, barcode, or name)
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_barang', 'like', "%{$keyword}%")
              ->orWhere('kode_sku', 'like', "%{$keyword}%")
              ->orWhere('barcode', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope for low stock products
     */
    public function scopeStokMenipis(Builder $query, int $threshold = 10): Builder
    {
        return $query->where('stok', '<=', $threshold);
    }
}
