<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk #{{ $transaction->invoice_number }} – Toko Kelontong</title>
    <style>
        @page {
            size: 58mm auto; /* Format standar printer thermal kasir */
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0 auto;
            padding: 8px 4px;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .store-title { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10px; }
        .item-row { margin-bottom: 4px; }
        .item-details { display: flex; justify-content: space-between; font-size: 10px; }
        .total-section { margin-top: 6px; }
        .footer { margin-top: 12px; font-size: 9px; }
        .btn-print {
            display: block;
            width: 100%;
            padding: 8px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-family: sans-serif;
            font-size: 12px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        @media print {
            .no-print { display: none; }
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk Sekarang</button>
    </div>

    <div class="text-center">
        <div class="store-title">TOKO KELONTONG</div>
        <div>Jl. Raya Kelontong Sejahtera</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="divider"></div>

    <div class="info-row">
        <span>No: {{ $transaction->invoice_number }}</span>
    </div>
    <div class="info-row">
        <span>Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        <span>Kasir: {{ $transaction->user->name ?? '-' }}</span>
    </div>

    <div class="divider"></div>

    <!-- DAFTAR BARANG -->
    @foreach($transaction->details as $d)
        <div class="item-row">
            <div class="bold">{{ $d->product_name }}</div>
            <div class="item-details">
                <span>{{ $d->quantity }} x {{ number_format($d->sell_price, 0, ',', '.') }}</span>
                <span>{{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="divider"></div>

    <!-- TOTAL KEUANGAN -->
    <div class="total-section">
        <div class="info-row">
            <span>Total Item:</span>
            <span>{{ $transaction->total_items }}</span>
        </div>
        <div class="info-row bold" style="font-size: 12px; margin-top: 2px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span>Tunai:</span>
            <span>Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row bold">
            <span>Kembali:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($transaction->notes)
        <div class="divider"></div>
        <div style="font-size: 9px; font-style: italic;">
            Catatan: {{ $transaction->notes }}
        </div>
    @endif

    <div class="divider"></div>

    <div class="text-center footer">
        <div>TERIMA KASIH</div>
        <div>ATAS KUNJUNGAN ANDA</div>
        <div style="margin-top: 4px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
    </div>

</body>
</html>

