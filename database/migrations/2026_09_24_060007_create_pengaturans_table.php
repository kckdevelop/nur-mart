<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko')->default('TOKO KELONTONG NURMART');
            $table->string('slogan')->nullable()->default('Sedia Sembako & Kebutuhan Rumah Tangga Terlengkap');
            $table->string('no_telepon')->nullable()->default('0812-3456-7890');
            $table->text('alamat')->nullable();
            $table->text('footer_struk')->nullable();
            $table->timestamps();
        });

        // Insert row data awal
        DB::table('pengaturans')->insert([
            'nama_toko' => 'TOKO KELONTONG NURMART',
            'slogan' => 'Sedia Sembako & Kebutuhan Rumah Tangga Terlengkap',
            'no_telepon' => '0812-3456-7890',
            'alamat' => 'Jogodayoh RT 02, Sedia Sembako & Kebutuhan Rumah Tangga',
            'footer_struk' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan. Terima kasih atas kunjungan Anda!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
