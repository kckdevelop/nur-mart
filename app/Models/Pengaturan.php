<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturans';

    protected $fillable = [
        'nama_toko',
        'slogan',
        'no_telepon',
        'alamat',
        'footer_struk',
    ];

    /**
     * Helper untuk mengambil data setting utama toko
     */
    public static function getUtama(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'nama_toko' => 'TOKO KELONTONG NURMART',
                'slogan' => 'Sedia Sembako & Kebutuhan Rumah Tangga Terlengkap',
                'no_telepon' => '0812-3456-7890',
                'alamat' => 'Jogodayoh RT 02, Sedia Sembako & Kebutuhan Rumah Tangga',
                'footer_struk' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan. Terima kasih atas kunjungan Anda!',
            ]
        );
    }
}
