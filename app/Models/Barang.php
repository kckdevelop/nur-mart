<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['gambar_full_url'];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'float',
            'harga_jual' => 'float',
            'stok' => 'integer',
        ];
    }

    /**
     * Accessor: selalu kembalikan full URL gambar produk.
     * Mendukung path relatif storage (barangs/xxx.jpg) maupun URL eksternal.
     */
    public function getGambarFullUrlAttribute(): ?string
    {
        if (empty($this->gambar_url)) {
            return null;
        }

        // Jika sudah berupa URL lengkap (http/https), kembalikan langsung
        if (str_starts_with($this->gambar_url, 'http')) {
            return $this->gambar_url;
        }

        // Path relatif di storage public → konversi ke full URL
        return url(Storage::url($this->gambar_url));
    }

    /**
     * Hapus file gambar dari storage (jika tersimpan lokal).
     */
    public function hapusGambar(): void
    {
        if (empty($this->gambar_url) || str_starts_with($this->gambar_url, 'http')) {
            // Abaikan jika gambar_url adalah URL eksternal atau kosong
            // Hanya hapus jika path relatif storage
            if (!empty($this->gambar_url) && !str_starts_with($this->gambar_url, url('/'))) {
                return;
            }

            // Ekstrak path dari full URL
            $relativePath = str_replace(url('/storage') . '/', '', $this->gambar_url);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
            return;
        }

        // Path relatif langsung
        if (Storage::disk('public')->exists($this->gambar_url)) {
            Storage::disk('public')->delete($this->gambar_url);
        }
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
