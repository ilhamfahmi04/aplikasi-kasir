@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi Penjualan')
@section('page_subtitle', 'Data seluruh faktur transaksi kasir toko yang telah selesai dilakukan')

@section('content')
<div class="space-y-6">

    <!-- FORMULIR PENCARIAN & FILTER TRANSAKSI -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. Invoice</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Contoh: TRX-2026..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kasir</label>
                <select name="user_id" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->role }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Filter</button>
                    @if($search || $userId || $startDate || $endDate)
                        <a href="{{ route('admin.transactions.index') }}" class="rounded-lg bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- TABEL TRANSAKSI -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Waktu Transaksi</th>
                        <th class="py-3 px-4">Kasir Bertugas</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right">Total Belanja</th>
                        <th class="py-3 px-4 text-right">Laba Keuntungan</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $trx->invoice_number }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $trx->user->name ?? 'Kasir Tidak Ada' }}</td>
                            <td class="py-3 px-4 text-center">{{ $trx->total_items }} item</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-emerald-600">Rp {{ number_format($trx->total_profit, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.transactions.show', $trx->id) }}"
                                       class="rounded bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-blue-600 hover:text-white transition" title="Lihat Rincian">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('kasir.print', $trx->id) }}" target="_blank"
                                       class="rounded bg-slate-100 p-1 text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition" title="Cetak Ulang Struk">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="bi bi-receipt text-3xl mb-1 block"></i>
                                Tidak ada transaksi yang sesuai dengan filter pencarian.
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

