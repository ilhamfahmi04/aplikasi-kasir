@extends('layouts.app')

@section('title', 'Riwayat Transaksi Saya')
@section('page_title', 'Riwayat Transaksi Penjualan Saya')
@section('page_subtitle', 'Daftar seluruh transaksi yang dilayani oleh kasir: ' . auth()->user()->name)

@section('content')
<div class="space-y-6">

    <!-- FORM PENCARIAN & FILTER -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('kasir.history') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Invoice..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-1.5 pl-9 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-500">Tanggal:</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Filter</button>
            @if($search || $date)
                <a href="{{ route('kasir.history') }}" class="rounded-lg bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>
    </div>

    <!-- TABEL RIWAYAT TRANSAKSI -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Waktu Transaksi</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right">Total Belanja</th>
                        <th class="py-3 px-4 text-right">Uang Diterima</th>
                        <th class="py-3 px-4 text-right">Kembalian</th>
                        <th class="py-3 px-4 text-center w-28">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $trx->invoice_number }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $trx->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 text-center font-medium">{{ $trx->total_items }} item</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($trx->cash_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-emerald-600">Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('kasir.print', $trx->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-600 hover:text-white transition">
                                    <i class="bi bi-printer"></i> Cetak Ulang
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="bi bi-receipt text-3xl mb-1 block"></i>
                                Belum ada transaksi penjualan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

