<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BelanjaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PenjualanController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Toko Kelontong NURMART (Backend for Flutter Mobile)
|--------------------------------------------------------------------------
*/

// ========================
// 1. PUBLIC ROUTES
// ========================
Route::post('/login', [AuthController::class, 'login']);
Route::get('/penjualan/{id}/cetak-struk', [PenjualanController::class, 'cetakStrukPdf']);

// ===================================
// 2. PROTECTED ROUTES (Sanctum Auth)
// ===================================
Route::middleware('auth:sanctum')->group(function () {

    // --- User Session Management ---
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Master Data (Read-only for all authenticated: Kasir & Pemilik) ---
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);

    Route::get('/barang/stok-menipis', [BarangController::class, 'stokMenipis']);
    Route::get('/barang', [BarangController::class, 'index']);
    Route::get('/barang/{id}', [BarangController::class, 'show']);

    Route::get('/supplier', [SupplierController::class, 'index']);
    Route::get('/supplier/{id}', [SupplierController::class, 'show']);

    // --- POS & Penjualan (Kasir & Pemilik) ---
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::post('/penjualan', [PenjualanController::class, 'store']);
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);

    // =========================================================================
    // 3. OWNER ONLY ROUTES (Role: pemilik)
    // =========================================================================
    Route::middleware('role:pemilik')->group(function () {

        // Master Data CRUD (Create, Update, Delete)
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        Route::post('/supplier', [SupplierController::class, 'store']);
        Route::put('/supplier/{id}', [SupplierController::class, 'update']);
        Route::delete('/supplier/{id}', [SupplierController::class, 'destroy']);

        Route::post('/barang', [BarangController::class, 'store']);
        Route::post('/barang/{id}', [BarangController::class, 'update']); // Support multipart/form-data upload
        Route::put('/barang/{id}', [BarangController::class, 'update']);
        Route::delete('/barang/{id}/gambar', [BarangController::class, 'destroyGambar']); // Hapus gambar saja
        Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

        // Purchasing / Belanja Barang
        Route::get('/belanja', [BelanjaController::class, 'index']);
        Route::post('/belanja', [BelanjaController::class, 'store']);
        Route::get('/belanja/{id}', [BelanjaController::class, 'show']);

        // Laporan & Dasbor
        Route::get('/laporan/dasbor', [LaporanController::class, 'dasbor']);
        Route::get('/laporan/laba-rugi', [LaporanController::class, 'labaRugi']);
    });
});
