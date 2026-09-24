<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $penjualan->no_nota }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto; /* lebar struk thermal 80mm, tinggi menyesuaikan isi */
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 8px 10px;
            width: 80mm;
            max-width: 80mm;
            overflow: hidden;
            word-wrap: break-word;
            word-break: break-word;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .bold        { font-weight: bold; }

        .store-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .store-address {
            font-size: 9px;
            margin-bottom: 4px;
            line-height: 1.5;
        }
        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-divider {
            border: none;
            border-top: 2px solid #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td {
            padding: 1px 0;
            vertical-align: top;
            overflow: hidden;
            word-wrap: break-word;
        }
        /* Kolom kiri label (40%) dan kolom kanan nilai (60%) */
        td:first-child  { width: 42%; }
        td:last-child   { width: 58%; }

        .item-name {
            font-weight: bold;
            white-space: normal;
        }
        .footer-note {
            font-size: 8.5px;
            margin-top: 8px;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="store-title">TOKO KELONTONG NURMART</div>
        <div class="store-address">
            Jogodayoh RT 02, Sedia Sembako &amp; Kebutuhan Rumah Tangga<br>
            Telp / WA: 0812-3456-7890
        </div>
    </div>

    <hr class="divider">

    <table>
        <colgroup>
            <col style="width: 42%;">
            <col style="width: 58%;">
        </colgroup>
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

    <hr class="divider">

    <table>
        <colgroup>
            <col style="width: 58%;">
            <col style="width: 42%;">
        </colgroup>
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

    <hr class="divider">

    <table>
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
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

    <hr class="double-divider">

    <div class="footer-note">
        *** TERIMA KASIH ATAS KUNJUNGAN ANDA ***<br>
        Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>
        Layanan Keluhan Pelanggan: 0812-3456-7890
    </div>
</body>
</html>
