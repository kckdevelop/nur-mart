<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Belanja\StoreBelanjaRequest;
use App\Models\Barang;
use App\Models\Belanja;
use App\Models\DetailBelanja;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BelanjaController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Belanja::with(['supplier', 'user', 'details.barang']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $perPage = (int) $request->get('per_page', 15);
        $belanjas = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage);

        return $this->successResponse($belanjas, 'Riwayat transaksi belanja berhasil diambil.');
    }

    public function store(StoreBelanjaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $belanja = DB::transaction(function () use ($validated, $request) {
                // Generate nomor faktur jika tidak diinput
                $todayCode = Carbon::now()->format('Ymd');
                $noFaktur = $validated['no_faktur_pembelian'] ?? null;

                if (empty($noFaktur)) {
                    $countToday = Belanja::whereDate('created_at', Carbon::today())->count() + 1;
                    $noFaktur = 'PB-' . $todayCode . '-' . str_pad((string)$countToday, 4, '0', STR_PAD_LEFT);
                }

                // Hitung total belanja dari akumulasi items
                $totalBelanja = 0;
                foreach ($validated['items'] as $item) {
                    $totalBelanja += ($item['jumlah'] * $item['harga_beli_satuan']);
                }

                // 1. Simpan Header Transaksi Belanja
                $belanja = Belanja::create([
                    'no_faktur_pembelian' => $noFaktur,
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => $request->user()->id,
                    'tanggal' => $validated['tanggal'],
                    'total_belanja' => $totalBelanja,
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                // 2. Simpan Detail Belanja & Otomatis Tambahkan Stok Barang
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['jumlah'] * $item['harga_beli_satuan'];

                    DetailBelanja::create([
                        'belanja_id' => $belanja->id,
                        'barang_id' => $item['barang_id'],
                        'jumlah' => $item['jumlah'],
                        'harga_beli_satuan' => $item['harga_beli_satuan'],
                        'subtotal' => $subtotal,
                    ]);

                    // Otomatis update stok dan harga beli terbaru pada produk
                    $barang = Barang::lockForUpdate()->find($item['barang_id']);
                    if ($barang) {
                        $barang->increment('stok', $item['jumlah']);
                        // Update harga beli ke harga pembelian terbaru
                        $barang->update(['harga_beli' => $item['harga_beli_satuan']]);
                    }
                }

                return $belanja;
            });

            $belanja->load(['supplier', 'user', 'details.barang']);

            return $this->successResponse($belanja, 'Transaksi belanja berhasil disimpan dan stok barang otomatis bertambah.', 201);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal memproses transaksi belanja: ' . $e->getMessage(), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $belanja = Belanja::with(['supplier', 'user', 'details.barang'])->find($id);

        if (!$belanja) {
            return $this->errorResponse('Data transaksi belanja tidak ditemukan.', 404);
        }

        return $this->successResponse($belanja, 'Detail transaksi belanja berhasil diambil.');
    }
}
