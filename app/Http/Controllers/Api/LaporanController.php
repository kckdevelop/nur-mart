<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Belanja;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    use ApiResponseTrait;

    /**
     * Rekap Ringkas Dasbor Toko Kelontong
     */
    public function dasbor(): JsonResponse
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Omset & Transaksi Hari Ini
        $penjualanHariIni = Penjualan::whereDate('tanggal', $today);
        $omsetHariIni = (float) $penjualanHariIni->sum('total_belanja');
        $transaksiHariIni = (int) $penjualanHariIni->count();

        // 2. Omset & Transaksi Bulan Ini
        $penjualanBulanIni = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $omsetBulanIni = (float) $penjualanBulanIni->sum('total_belanja');
        $transaksiBulanIni = (int) $penjualanBulanIni->count();

        // 3. Margin / Keuntungan Bulan Ini (Omset Penjualan - Modal HPP Barang Terjual)
        $marginBulanIni = (float) DetailPenjualan::join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('barang', 'detail_penjualan.barang_id', '=', 'barang.id')
            ->whereBetween('penjualan.tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw('SUM((detail_penjualan.harga_jual_satuan - barang.harga_beli) * detail_penjualan.jumlah) as margin')
            ->value('margin') ?? 0;

        // 4. Monitoring Stok Menipis (stok <= 10)
        $lowStockQuery = Barang::with('kategori')->stokMenipis(10);
        $totalStokMenipis = $lowStockQuery->count();
        $daftarStokMenipis = $lowStockQuery->orderBy('stok', 'asc')->limit(10)->get();

        $totalProduk = Barang::count();

        $data = [
            'hari_ini' => [
                'tanggal' => $today->format('Y-m-d'),
                'total_omset' => $omsetHariIni,
                'total_transaksi' => $transaksiHariIni,
            ],
            'bulan_ini' => [
                'bulan' => Carbon::now()->isoFormat('MMMM Y'),
                'total_omset' => $omsetBulanIni,
                'total_transaksi' => $transaksiBulanIni,
                'total_keuntungan_margin' => $marginBulanIni,
            ],
            'inventaris' => [
                'total_produk' => $totalProduk,
                'total_stok_menipis' => $totalStokMenipis,
                'daftar_stok_menipis' => $daftarStokMenipis,
            ],
        ];

        return $this->successResponse($data, 'Data ringkasan dasbor berhasil dimuat.');
    }

    /**
     * Laporan Laba Rugi Sederhana (Penjualan vs HPP / Belanja)
     */
    public function labaRugi(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        // 1. Total Penjualan (Revenue)
        $totalPenjualan = (float) Penjualan::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_belanja');

        // 2. Breakdown Metode Pembayaran
        $penjualanTunai = (float) Penjualan::whereBetween('tanggal', [$startDate, $endDate])
            ->where('metode_pembayaran', 'tunai')
            ->sum('total_belanja');

        $penjualanQris = (float) Penjualan::whereBetween('tanggal', [$startDate, $endDate])
            ->where('metode_pembayaran', 'qris')
            ->sum('total_belanja');

        // 3. Total Belanja / Pengadaan Stok (Purchasing Cash Out)
        $totalBelanja = (float) Belanja::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_belanja');

        // 4. HPP Barang Terjual (Cost of Goods Sold based on sales)
        $totalHppTerjual = (float) DetailPenjualan::join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('barang', 'detail_penjualan.barang_id', '=', 'barang.id')
            ->whereBetween('penjualan.tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(barang.harga_beli * detail_penjualan.jumlah) as hpp')
            ->value('hpp') ?? 0;

        // 5. Margin Kotor (Omset - HPP Terjual)
        $labaKotor = $totalPenjualan - $totalHppTerjual;

        // 6. Arus Kas Bersih (Penjualan - Pengeluaran Belanja Supplier)
        $arusKasBersih = $totalPenjualan - $totalBelanja;

        $data = [
            'periode' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'pendapatan_penjualan' => [
                'total_penjualan' => $totalPenjualan,
                'metode_tunai' => $penjualanTunai,
                'metode_qris' => $penjualanQris,
            ],
            'pengeluaran_dan_hpp' => [
                'total_belanja_supplier' => $totalBelanja,
                'hpp_barang_terjual' => $totalHppTerjual,
            ],
            'laba_rugi' => [
                'laba_kotor_penjualan' => $labaKotor,
                'arus_kas_operasional' => $arusKasBersih,
            ],
        ];

        return $this->successResponse($data, 'Laporan laba rugi berhasil diambil.');
    }
}
