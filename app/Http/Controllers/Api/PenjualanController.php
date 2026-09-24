<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penjualan\StorePenjualanRequest;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Traits\ApiResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Penjualan::with(['kasir', 'details.barang']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->filterDate($request->start_date, $request->end_date);
        }

        if ($request->filled('kasir_id')) {
            $query->where('kasir_id', $request->kasir_id);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $perPage = (int) $request->get('per_page', 15);
        $penjualans = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage);

        return $this->successResponse($penjualans, 'Daftar transaksi penjualan berhasil diambil.');
    }

    public function store(StorePenjualanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $penjualan = DB::transaction(function () use ($validated, $request) {
                // 1. Verifikasi ketersediaan stok & siapkan data item
                $totalBelanja = 0;
                $processedItems = [];

                foreach ($validated['items'] as $item) {
                    $barang = Barang::lockForUpdate()->find($item['barang_id']);

                    if (!$barang) {
                        throw new Exception("Barang dengan ID {$item['barang_id']} tidak ditemukan.");
                    }

                    if ($barang->stok < $item['jumlah']) {
                        throw new Exception("Stok '{$barang->nama_barang}' tidak mencukupi. Sisa stok: {$barang->stok}, diminta: {$item['jumlah']}.");
                    }

                    $hargaJualSatuan = $item['harga_jual_satuan'] ?? $barang->harga_jual;
                    $subtotal = $item['jumlah'] * $hargaJualSatuan;
                    $totalBelanja += $subtotal;

                    $processedItems[] = [
                        'barang' => $barang,
                        'jumlah' => $item['jumlah'],
                        'harga_jual_satuan' => $hargaJualSatuan,
                        'subtotal' => $subtotal,
                    ];
                }

                // 2. Validasi jumlah bayar
                $jumlahBayar = (float) $validated['jumlah_bayar'];
                if ($jumlahBayar < $totalBelanja) {
                    throw new Exception("Jumlah pembayaran (Rp " . number_format($jumlahBayar, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalBelanja, 0, ',', '.') . ").");
                }

                $kembalian = $jumlahBayar - $totalBelanja;

                // 3. Generate nomor nota unik: PJ-YYYYMMDD-XXXX
                $todayCode = Carbon::now()->format('Ymd');
                $countToday = Penjualan::whereDate('created_at', Carbon::today())->count() + 1;
                $noNota = 'PJ-' . $todayCode . '-' . str_pad((string)$countToday, 4, '0', STR_PAD_LEFT);

                // 4. Simpan Header Penjualan
                $penjualan = Penjualan::create([
                    'no_nota' => $noNota,
                    'tanggal' => $validated['tanggal'] ?? Carbon::now(),
                    'kasir_id' => $request->user()->id,
                    'total_belanja' => $totalBelanja,
                    'jumlah_bayar' => $jumlahBayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                ]);

                // 5. Simpan Detail Penjualan dan kurangi stok barang
                foreach ($processedItems as $pItem) {
                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $pItem['barang']['id'],
                        'jumlah' => $pItem['jumlah'],
                        'harga_jual_satuan' => $pItem['harga_jual_satuan'],
                        'subtotal' => $pItem['subtotal'],
                    ]);

                    // Otomatis kurangi stok barang
                    $pItem['barang']->decrement('stok', $pItem['jumlah']);
                }

                return $penjualan;
            });

            $penjualan->load(['kasir', 'details.barang']);

            return $this->successResponse($penjualan, 'Transaksi penjualan berhasil disimpan dan stok otomatis dipotong.', 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        $penjualan = Penjualan::with(['kasir', 'details.barang'])->find($id);

        if (!$penjualan) {
            return $this->errorResponse('Transaksi penjualan tidak ditemukan.', 404);
        }

        return $this->successResponse($penjualan, 'Detail transaksi penjualan berhasil diambil.');
    }

    /**
     * Endpoint cetak / download struk PDF belanja untuk mobile
     */
    public function cetakStrukPdf(int $id)
    {
        $penjualan = Penjualan::with(['kasir', 'details.barang'])->find($id);

        if (!$penjualan) {
            return $this->errorResponse('Transaksi penjualan tidak ditemukan.', 404);
        }

        // Estimasi tinggi kertas roll thermal dinamis dan presisi (dengan margin tepi aman)
        $itemCount = $penjualan->details->count();
        $paperHeight = 250 + ($itemCount * 22); // pt

        // 80mm width ≈ 226.77 pt
        $customPaper = [0, 0, 226.77, $paperHeight];

        $pengaturan = \App\Models\Pengaturan::getUtama();

        $pdf = Pdf::loadView('pdf.struk_penjualan', compact('penjualan', 'pengaturan'))
            ->setPaper($customPaper, 'portrait');

        $fileName = 'struk_' . $penjualan->no_nota . '.pdf';

        return $pdf->stream($fileName, [
            'Attachment' => false,
            'Content-Type' => 'application/pdf',
        ]);
    }
}
