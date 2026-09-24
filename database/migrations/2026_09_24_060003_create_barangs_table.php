<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sku', 50)->unique();
            $table->string('barcode', 100)->nullable()->index();
            $table->string('nama_barang', 150)->index();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->integer('stok')->default(0)->index();
            $table->string('satuan', 20)->default('pcs'); // pcs, kg, dus, botol, pack, renteng
            $table->string('gambar_url')->nullable();
            $table->timestamps();

            $table->index(['nama_barang', 'kategori_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
