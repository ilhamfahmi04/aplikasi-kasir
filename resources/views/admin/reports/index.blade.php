@extends('layouts.app')

@section('title', 'Laporan Rekap Penjualan')
@section('page_title', 'Laporan Rekapitulasi Penjualan')
@section('page_subtitle', 'Analisis komprehensif omset, total modal, laba bersih keuntungan toko, dan produk paling laris')

@section('content')
<div class="space-y-6">

    <!-- FORMULIR FILTER PERIODE LAPORAN -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-5" x-data="{ mode: '{{ $periodType }}' }">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-xs font-bold text-slate-700">Pilih Jenis Periode:</label>
                <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                    <input type="radio" name="period_type" value="weekly" x-model="mode" {{ $periodType === 'weekly' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span>Rekap Minggu Ini</span>
                </label>
                <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                    <input type="radio" name="period_type" value="monthly" x-model="mode" {{ $periodType === 'monthly' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span>Rekap Bulanan</span>
                </label>
                <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                    <input type="radio" name="period_type" value="custom" x-model="mode" {{ $periodType === 'custom' ? 'checked' : '' }}>
                    <span>Kustom Tanggal</span>
                </label>
            </div>

            <!-- Input Dinamis Berdasarkan Periode -->
            <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-slate-100">
                <div x-show="mode === 'monthly'" class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">Pilih Bulan & Tahun:</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                </div>

                <div x-show="mode === 'custom'" class="flex flex-wrap items-center gap-2">
                    <label class="text-xs text-slate-500">Dari:</label>
                    <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <label class="text-xs text-slate-500">Sampai:</label>
                    <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Tampilkan</button>
                </div>

                <!-- Tombol Cetak & Export -->
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('admin.reports.print', ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')]) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 transition shadow">
                        <i class="bi bi-printer"></i> Cetak / PDF
                    </a>
                    <a href="{{ route('admin.reports.export-csv', ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel (CSV)
                    </a>
                </div>
            </div>

        </form>
    </div>

    <!-- KARTU STATISTIK HASIL LAPORAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Transaksi -->
        <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <span class="text-[11px] font-semibold uppercase text-slate-400">Total Transaksi</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">{{ number_format($totalTransactions) }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Struk penjualan</p>
        </div>

        <!-- Total Barang Terjual -->
        <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <span class="text-[11px] font-semibold uppercase text-slate-400">Barang Terjual</span>
            <h3 class="text-xl font-bold text-slate-800 mt-1">{{ number_format($totalItemsSold) }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Total unit barang</p>
        </div>

        <!-- Total Pendapatan / Omset -->
        <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <span class="text-[11px] font-semibold uppercase text-slate-400">Total Pendapatan</span>
            <h3 class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Omset kotor</p>
        </div>

        <!-- Total Modal Kulak -->
        <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
            <span class="text-[11px] font-semibold uppercase text-slate-400">Modal Barang</span>
            <h3 class="text-xl font-bold text-slate-700 mt-1">Rp {{ number_format($totalCost, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Harga beli modal</p>
        </div>

        <!-- Total Keuntungan Bersih -->
        <div class="rounded-xl bg-white p-4 shadow-sm border border-emerald-200 bg-emerald-50/20">
            <span class="text-[11px] font-semibold uppercase text-emerald-600">Total Keuntungan</span>
            <h3 class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-emerald-700 mt-1">Laba kotor bersih</p>
        </div>

    </div>

    <!-- BARIS KEDUA: PRODUK TERLARIS & HARI PENJUALAN TERTINGGI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 5 Produk Terlaris -->
        <div class="lg:col-span-2 rounded-xl bg-white p-5 shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i class="bi bi-trophy text-amber-500"></i> Produk Paling Laris (Top Selling)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                        <tr>
                            <th class="py-2 px-3">Nama Produk</th>
                            <th class="py-2 px-3 text-center">Terjual</th>
                            <th class="py-2 px-3 text-right">Total Nilai Penjualan</th>
                            <th class="py-2 px-3 text-right">Kontribusi Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topProducts as $tp)
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $tp->product_name }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-blue-600">{{ $tp->total_qty }} pcs</td>
                                <td class="py-2.5 px-3 text-right text-slate-700">Rp {{ number_format($tp->total_sales, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-emerald-600">Rp {{ number_format($tp->total_profit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">Belum ada barang terjual pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tanggal Penjualan Tertinggi -->
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="bi bi-star text-yellow-500"></i> Puncak Penjualan Tertinggi
                </h3>
                @if($highestDay)
                    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 mt-2">
                        <span class="text-xs text-blue-700 font-semibold">Tanggal Rekor:</span>
                        <h4 class="text-lg font-bold text-slate-800">{{ \Carbon\Carbon::parse($highestDay->date)->translatedFormat('l, d F Y') }}</h4>
                        <div class="mt-3 pt-3 border-t border-blue-200/60 space-y-1 text-xs">
                            <p class="text-slate-600">Total Penjualan: <span class="font-bold text-blue-700">Rp {{ number_format($highestDay->total_sales, 0, ',', '.') }}</span></p>
                            <p class="text-slate-600">Jumlah Transaksi: <span class="font-bold text-slate-800">{{ $highestDay->total_trx }} transaksi</span></p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Belum ada data penjualan pada periode ini.</p>
                @endif
            </div>
            <div class="text-[11px] text-slate-400 mt-4">
                Periode data: {{ $start->format('d/m/Y') }} s/d {{ $end->format('d/m/Y') }}
            </div>
        </div>

    </div>

    <!-- RINCIAN TRANSAKSI DALAM PERIODE -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rincian Faktur Transaksi Penjualan</h3>
            <span class="text-xs text-slate-500">{{ $transactions->count() }} Transaksi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                    <tr>
                        <th class="py-2.5 px-4">No. Invoice</th>
                        <th class="py-2.5 px-4">Waktu</th>
                        <th class="py-2.5 px-4">Kasir</th>
                        <th class="py-2.5 px-4 text-center">Qty Item</th>
                        <th class="py-2.5 px-4 text-right">Omset (Rp)</th>
                        <th class="py-2.5 px-4 text-right">Modal (Rp)</th>
                        <th class="py-2.5 px-4 text-right">Laba Keuntungan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 font-mono font-semibold text-blue-600">{{ $t->invoice_number }}</td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-4 text-slate-700">{{ $t->user->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-center">{{ $t->total_items }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-slate-800">{{ number_format($t->total_amount, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-right text-slate-500">{{ number_format($t->total_cost, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-emerald-600">{{ number_format($t->total_profit, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada transaksi tercatat pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

