<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Penjualan – Toko Kelontong</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 5px 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; color: #555; }
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px; }
        .summary-item { text-align: center; }
        .summary-item .title { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .summary-item .val { font-size: 14px; font-weight: bold; margin-top: 4px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; font-size: 11px; }
        .signature { text-align: center; width: 200px; margin-top: 40px; border-top: 1px solid #333; padding-top: 5px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Dokumen / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 8px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>SISTEM KASIR TOKO KELONTONG</h1>
        <p><strong>LAPORAN REKAPITULASI PENJUALAN & LABA KEUNTUNGAN</strong></p>
        <p>Periode: {{ $start->format('d/m/Y') }} s/d {{ $end->format('d/m/Y') }} | Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="title">Total Transaksi</div>
            <div class="val">{{ number_format($totalTransactions) }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Barang Terjual</div>
            <div class="val">{{ number_format($totalItemsSold) }} unit</div>
        </div>
        <div class="summary-item">
            <div class="title">Total Pendapatan (Omset)</div>
            <div class="val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Total Modal Kulak</div>
            <div class="val">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Keuntungan Bersih</div>
            <div class="val" style="color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>No. Invoice</th>
                <th>Waktu Transaksi</th>
                <th>Kasir</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-right">Omset Penjualan</th>
                <th class="text-right">Modal Pokok</th>
                <th class="text-right">Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $t->invoice_number }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $t->total_items }}</td>
                    <td class="text-right">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($t->total_cost, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #059669;">Rp {{ number_format($t->total_profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-center">{{ number_format($totalItemsSold) }}</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>
            <p>Catatan:</p>
            <p>- Laba keuntungan dihitung berdasarkan selisih harga jual dan harga beli saat transaksi terjadi.</p>
        </div>
        <div>
            <p>Mengetahui,</p>
            <div class="signature">
                Pemilik Toko Kelontong
            </div>
        </div>
    </div>

</body>
</html>

