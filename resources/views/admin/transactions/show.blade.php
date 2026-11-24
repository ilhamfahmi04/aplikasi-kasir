@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $transaction->invoice_number)
@section('page_title', 'Detail Faktur Transaksi')
@section('page_subtitle', 'Rincian transaksi penjualan #' . $transaction->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER AKSI -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Transaksi
        </a>
        <a href="{{ route('kasir.print', $transaction->id) }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-700 transition">
            <i class="bi bi-printer"></i> Cetak Ulang Struk
        </a>
    </div>

    <!-- KARTU INFORMASI UTAMA -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400">Nomor Invoice</span>
                <p class="text-base font-mono font-bold text-blue-600">{{ $transaction->invoice_number }}</p>
            </div>
            <div>
                <span class="text-xs text-slate-400">Waktu Transaksi</span>
                <p class="text-sm font-semibold text-slate-800">{{ $transaction->created_at->format('d/m/Y H:i:s') }} WIB</p>
            </div>
            <div>
                <span class="text-xs text-slate-400">Kasir yang Melayani</span>
                <p class="text-sm font-semibold text-slate-800">{{ $transaction->user->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-slate-400">Total Keuntungan</span>
                <p class="text-sm font-bold text-emerald-600">+ Rp {{ number_format($transaction->total_profit, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- TABEL RINCIAN BARANG -->
        <div class="mt-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Daftar Barang yang Dibeli</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-3">Kode</th>
                            <th class="py-2.5 px-3">Nama Produk</th>
                            <th class="py-2.5 px-3 text-right">Harga Beli (Modal)</th>
                            <th class="py-2.5 px-3 text-right">Harga Jual</th>
                            <th class="py-2.5 px-3 text-center">Jumlah</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                            <th class="py-2.5 px-3 text-right">Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transaction->details as $d)
                            <tr>
                                <td class="py-2.5 px-3 font-mono text-slate-500">{{ $d->product_code }}</td>
                                <td class="py-2.5 px-3 font-bold text-slate-800">{{ $d->product_name }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-400">Rp {{ number_format($d->buy_price, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-700">Rp {{ number_format($d->sell_price, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-center font-bold">{{ $d->quantity }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-slate-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right font-semibold text-emerald-600">Rp {{ number_format($d->subtotal_profit, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RINGKASAN TOTAL PEMBAYARAN -->
        <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:justify-end">
            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Total Kuantitas:</span>
                    <span class="font-bold text-slate-800">{{ $transaction->total_items }} barang</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Total Belanja:</span>
                    <span class="font-bold text-base text-blue-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Uang Diterima Tunai:</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Uang Kembalian:</span>
                    <span class="font-bold text-emerald-600">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

