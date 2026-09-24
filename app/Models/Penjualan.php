<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_nota',
        'tanggal',
        'kasir_id',
        'total_belanja',
        'jumlah_bayar',
        'kembalian',
        'metode_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total_belanja' => 'float',
            'jumlah_bayar' => 'float',
            'kembalian' => 'float',
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereYear('tanggal', Carbon::now()->year)
                     ->whereMonth('tanggal', Carbon::now()->month);
    }

    public function scopeFilterDate(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            return $query->where('tanggal', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            return $query->where('tanggal', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query;
    }
}
