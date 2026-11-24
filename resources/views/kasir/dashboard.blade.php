@extends('layouts.app')

@section('title', 'Dashboard Kasir')
@section('page_title', 'Dashboard Kasir')
@section('page_subtitle', 'Selamat bertugas, ' . auth()->user()->name . '! Layani pelanggan dengan ramah dan teliti')

@section('content')
<div class="space-y-6">

    <!-- HERO BANNER KASIR -->
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 via-blue-800 to-slate-900 p-6 md:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-xl">
            <span class="inline-block rounded-full bg-blue-500/30 px-3 py-1 text-xs font-semibold text-blue-200 mb-3 border border-blue-400/30">
                Poin Penjualan (POS) Aktif
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Siap Melayani Pelanggan?</h2>
            <p class="mt-2 text-sm text-blue-100">
                Buka terminal kasir sekarang untuk mulai memindai barcode barang, menghitung total belanja dan kembalian secara otomatis.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('kasir.transaction') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-xs font-bold text-blue-900 shadow-md hover:bg-blue-50 transition transform active:scale-95">
                    <i class="bi bi-cart3 text-base"></i> Buka Transaksi Kasir (POS)
                </a>
                <a href="{{ route('kasir.history') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-blue-600/60 border border-blue-400/40 px-4 py-3 text-xs font-semibold text-white hover:bg-blue-600 transition">
                    <i class="bi bi-clock-history"></i> Riwayat Transaksi Saya
                </a>
            </div>
        </div>
        <div class="absolute right-6 -bottom-6 text-blue-500/10 hidden md:block">
            <i class="bi bi-shop text-[180px]"></i>
        </div>
    </div>

    <!-- STATISTIK KASIR HARI INI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Total Penjualan Saya Hari Ini</span>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-1">Rp {{ number_format($mySalesToday, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <i class="bi bi-cash-stack text-2xl"></i>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Jumlah Transaksi Saya Hari Ini</span>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $myTrxTodayCount }}</h3>
                <p class="text-xs text-slate-500 mt-1">Transaksi yang Anda selesaikan</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i class="bi bi-check2-circle text-2xl"></i>
            </div>
        </div>

    </div>

    <!-- TRANSAKSI SAYA TERAKHIR -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Transaksi Terakhir yang Anda Layani</h3>
                <p class="text-xs text-slate-500">Daftar transaksi terbaru dari akun Anda</p>
            </div>
            <a href="{{ route('kasir.history') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                Lihat Semua <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right">Total Belanja</th>
                        <th class="py-3 px-4 text-right">Tunai Diterima</th>
                        <th class="py-3 px-4 text-right">Kembalian</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $t->invoice_number }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $t->created_at->format('H:i:s d/m/Y') }}</td>
                            <td class="py-3 px-4 text-center font-semibold">{{ $t->total_items }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($t->cash_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-emerald-600">Rp {{ number_format($t->change_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('kasir.print', $t->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 hover:bg-emerald-600 hover:text-white transition">
                                    <i class="bi bi-printer"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="bi bi-inbox text-3xl mb-1 block"></i>
                                Anda belum memiliki transaksi yang tercatat hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

