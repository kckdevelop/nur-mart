<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $penjualan->no_nota }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 16px 22px 20px 22px; /* Margin nyata di setiap tepi: atas 16px, kanan 22px, bawah 20px, kiri 22px */
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            line-height: 1.35;
            color: #000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .bold        { font-weight: bold; }

        /* Store Header */
        .store-header {
            text-align: center;
            margin-bottom: 5px;
        }
        .store-title {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .store-address {
            font-size: 7.5px;
            line-height: 1.3;
            color: #222;
        }

        /* Dividers */
        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-divider {
            border: none;
            border-top: 1.5px solid #000;
            margin: 5px 0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 8px;
        }

        /* Metadata Transaksi Nota */
        .tbl-meta td.label {
            width: 35%;
            text-align: left;
            color: #333;
        }
        .tbl-meta td.value {
            width: 65%;
            text-align: right;
            word-break: break-all;
        }

        /* Item Barang */
        .item-name {
            font-size: 8px;
            font-weight: bold;
            padding-top: 2px;
        }
        .tbl-item td.qty {
            width: 48%;
            text-align: left;
            color: #444;
            font-size: 7.5px;
        }
        .tbl-item td.subtotal {
            width: 52%;
            text-align: right;
            font-weight: bold;
        }

        /* Total & Pembayaran */
        .tbl-total td.label {
            width: 46%;
            text-align: left;
        }
        .tbl-total td.value {
            width: 54%;
            text-align: right;
        }
        .total-row td {
            font-size: 9px;
            font-weight: bold;
            padding: 2px 0;
        }

        /* Footer */
        .footer-note {
            font-size: 7px;
            text-align: center;
            line-height: 1.35;
            margin-top: 5px;
            color: #222;
        }
    </style>
</head>
<body>
    <!-- 1. Header Toko -->
    <div class="store-header">
        <div class="store-title">{{ $pengaturan->nama_toko ?? 'TOKO KELONTONG NURMART' }}</div>
        <div class="store-address">
            @if(!empty($pengaturan->slogan))
                {{ $pengaturan->slogan }}<br>
            @endif
            @if(!empty($pengaturan->alamat))
                {{ $pengaturan->alamat }}<br>
            @endif
            @if(!empty($pengaturan->no_telepon))
                Telp / WA: {{ $pengaturan->no_telepon }}
            @endif
        </div>
    </div>

    <hr class="divider">

    <!-- 2. Metadata Transaksi Nota -->
    <table class="tbl-meta">
        <tr>
            <td class="label">No. Nota</td>
            <td class="value bold">{{ $penjualan->no_nota }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Kasir</td>
            <td class="value">{{ $penjualan->kasir->name ?? 'Kasir' }}</td>
        </tr>
        <tr>
            <td class="label">Metode</td>
            <td class="value bold" style="text-transform: uppercase;">{{ $penjualan->metode_pembayaran }}</td>
        </tr>
    </table>

    <hr class="divider">

    <!-- 3. Rincian Item Barang -->
    <table class="tbl-item">
        @foreach($penjualan->details as $item)
            <tr>
                <td colspan="2" class="item-name">{{ $item->barang->nama_barang ?? 'Barang' }}</td>
            </tr>
            <tr>
                <td class="qty">
                    {{ $item->jumlah }} x Rp {{ number_format($item->harga_jual_satuan, 0, ',', '.') }}
                </td>
                <td class="subtotal">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <hr class="divider">

    <!-- 4. Rincian Pembayaran & Kembalian -->
    <table class="tbl-total">
        <tr class="total-row">
            <td class="label">TOTAL BELANJA</td>
            <td class="value">Rp {{ number_format($penjualan->total_belanja, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Bayar ({{ strtoupper($penjualan->metode_pembayaran) }})</td>
            <td class="value bold">Rp {{ number_format($penjualan->jumlah_bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label bold">Kembalian</td>
            <td class="value bold">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr class="double-divider">

    <!-- 5. Footer & Catatan Pelanggan -->
    <div class="footer-note">
        *** TERIMA KASIH ATAS KUNJUNGAN ANDA ***<br>
        @if(!empty($pengaturan->footer_struk))
            {!! nl2br(e($pengaturan->footer_struk)) !!}<br>
        @else
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>
        @endif
        @if(!empty($pengaturan->no_telepon))
            Layanan Pelanggan: {{ $pengaturan->no_telepon }}
        @endif
    </div>
</body>
</html>
