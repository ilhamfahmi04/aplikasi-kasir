@extends('layouts.app')

@section('title', 'Monitoring Kedaluwarsa')
@section('page_title', 'Monitoring Barang Kedaluwarsa')
@section('page_subtitle', 'Sistem pengawasan tanggal kedaluwarsa barang untuk menjamin kualitas dan kepatuhan penjualan')

@section('content')
<div class="space-y-6">

    <!-- KARTU STATUS KEDALUWARSA -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Sudah Kedaluwarsa -->
        <a href="{{ route('admin.expiry.index', ['status' => 'expired']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'expired' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-slate-200' }} hover:border-red-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-red-600">Sudah Kedaluwarsa</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <i class="bi bi-x-circle-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-red-600">{{ $expiredCount }}</h3>
                <p class="mt-1 text-xs text-red-700 font-medium">DILARANG DIJUAL - Segera buang/retur</p>
            </div>
        </a>

        <!-- Kritis <= 7 Hari -->
        <a href="{{ route('admin.expiry.index', ['status' => 'critical_7']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'critical_7' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200' }} hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-amber-600">Kritis (≤ 7 Hari)</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-amber-600">{{ $critical7Count }}</h3>
                <p class="mt-1 text-xs text-amber-700 font-medium">Beri diskon cuci gudang / pindah ke depan</p>
            </div>
        </a>

        <!-- Peringatan <= 30 Hari -->
        <a href="{{ route('admin.expiry.index', ['status' => 'warning_30']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'warning_30' ? 'border-yellow-500 ring-2 ring-yellow-500/20' : 'border-slate-200' }} hover:border-yellow-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-yellow-700">Peringatan (≤ 30 Hari)</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-yellow-700">{{ $warning30Count }}</h3>
                <p class="mt-1 text-xs text-yellow-800 font-medium">Pantau perputaran stok barang</p>
            </div>
        </a>

    </div>

    <!-- BARIS FILTER & PENCARIAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.expiry.index') }}" class="flex items-center gap-2 max-w-md w-full">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang atau barcode..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            @if($search)
                <a href="{{ route('admin.expiry.index', ['status' => $status]) }}" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-1.5 bg-slate-200 p-1 rounded-lg text-xs">
            <a href="{{ route('admin.expiry.index', ['status' => 'all']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Semua
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'expired']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'expired' ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Kedaluwarsa
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'critical_7']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'critical_7' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ≤ 7 Hari
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'warning_30']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'warning_30' ? 'bg-yellow-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ≤ 30 Hari
            </a>
        </div>
    </div>

    <!-- TABEL MONITORING KEDALUWARSA -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Kode Barcode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-center">Tanggal Kedaluwarsa</th>
                        <th class="py-3 px-4 text-center">Sisa Waktu</th>
                        <th class="py-3 px-4 text-center">Status & Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        @php
                            $days = $p->days_until_expiration;
                            $expStatus = $p->expiry_status;
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition {{ $expStatus === 'expired' ? 'bg-red-50/40' : ($expStatus === 'critical_7' ? 'bg-amber-50/40' : '') }}">
                            <td class="py-3 px-4 font-mono font-semibold text-blue-600">{{ $p->code }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $p->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $p->category->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">{{ $p->stock }} {{ $p->unit }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-700">
                                {{ $p->expired_date->format('d F Y') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($days < 0)
                                    <span class="font-bold text-red-600">Lewat {{ abs($days) }} hari lalu</span>
                                @elseif($days === 0)
                                    <span class="font-bold text-red-600">Hari ini!</span>
                                @else
                                    <span class="font-semibold {{ $days <= 7 ? 'text-amber-600' : 'text-slate-600' }}">{{ $days }} hari lagi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($expStatus === 'expired')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-[11px] font-bold text-red-700">
                                        <i class="bi bi-x-octagon-fill"></i> DILARANG DIJUAL (Kedaluwarsa)
                                    </span>
                                @elseif($expStatus === 'critical_7')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-[11px] font-bold text-red-600">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Sangat Mendesak (≤ 7 Hari)
                                    </span>
                                @elseif($expStatus === 'warning_30')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-[11px] font-semibold text-amber-700">
                                        <i class="bi bi-clock-fill"></i> Waspada (≤ 30 Hari)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                        <i class="bi bi-check-circle-fill"></i> Masa Simpan Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="bi bi-calendar2-check text-3xl mb-1 block"></i>
                                Tidak ada data barang yang sesuai dengan filter kedaluwarsa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

