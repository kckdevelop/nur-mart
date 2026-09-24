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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota', 100)->unique();
            $table->dateTime('tanggal')->index();
            $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
            $table->decimal('total_belanja', 14, 2)->default(0);
            $table->decimal('jumlah_bayar', 14, 2)->default(0);
            $table->decimal('kembalian', 14, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'qris'])->default('tunai')->index();
            $table->timestamps();
        });

        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->restrictOnDelete();
            $table->integer('jumlah')->unsigned();
            $table->decimal('harga_jual_satuan', 12, 2)->unsigned();
            $table->decimal('subtotal', 14, 2)->unsigned();
            $table->timestamps();

            $table->index(['penjualan_id', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('penjualan');
    }
};
