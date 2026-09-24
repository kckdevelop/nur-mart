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
        Schema::create('belanja', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur_pembelian', 100)->unique();
            $table->foreignId('supplier_id')->constrained('supplier')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal')->index();
            $table->decimal('total_belanja', 14, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('belanja_id')->constrained('belanja')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->restrictOnDelete();
            $table->integer('jumlah')->unsigned();
            $table->decimal('harga_beli_satuan', 12, 2)->unsigned();
            $table->decimal('subtotal', 14, 2)->unsigned();
            $table->timestamps();

            $table->index(['belanja_id', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_belanja');
        Schema::dropIfExists('belanja');
    }
};
