<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $penjualan->no_nota }}</title>
    <style>
        @page {
            margin: 0;
            padding: 5px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .store-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .store-address {
            font-size: 9px;
            margin-bottom: 6px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
        }
        .footer-note {
            font-size: 8.5px;
            margin-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="store-title">TOKO KELONTONG NURMART</div>
        <div class="store-address">
            Jl. Raya Utama No. 88, Sedia Sembako & Kebutuhan Rumah Tangga<br>
            Telp / WA: 0812-3456-7890
        </div>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left">No. Nota</td>
            <td class="text-right bold">{{ $penjualan->no_nota }}</td>
        </tr>
        <tr>
            <td class="text-left">Tanggal</td>
            <td class="text-right">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="text-left">Kasir</td>
            <td class="text-right">{{ $penjualan->kasir->name ?? 'Kasir' }}</td>
        </tr>
        <tr>
            <td class="text-left">Metode Bayar</td>
            <td class="text-right bold" style="text-transform: uppercase;">{{ $penjualan->metode_pembayaran }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach($penjualan->details as $item)
            <tr>
                <td colspan="2" class="item-name">{{ $item->barang->nama_barang ?? 'Barang' }}</td>
            </tr>
            <tr>
                <td class="text-left" style="color: #444;">
                    {{ $item->jumlah }} x Rp {{ number_format($item->harga_jual_satuan, 0, ',', '.') }}
                </td>
                <td class="text-right bold">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left bold" style="font-size: 11px;">TOTAL BELANJA</td>
            <td class="text-right bold" style="font-size: 11px;">Rp {{ number_format($penjualan->total_belanja, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left">Bayar ({{ strtoupper($penjualan->metode_pembayaran) }})</td>
            <td class="text-right">Rp {{ number_format($penjualan->jumlah_bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left bold">Kembalian</td>
            <td class="text-right bold">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="double-divider"></div>

    <div class="footer-note">
        *** TERIMA KASIH ATAS KUNJUNGAN ANDA ***<br>
        Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>
        Layanan Keluhan Pelanggan: 0812-3456-7890
    </div>
</body>
</html>
