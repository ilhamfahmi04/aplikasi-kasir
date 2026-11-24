@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Toko')
@section('page_subtitle', 'Ringkasan operasional penjualan, keuangan, dan status inventaris toko')

@section('content')
<div class="space-y-6">

    <!-- BARIS 1: KARTU STATISTIK KEUANGAN & PENJUALAN -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        
        <!-- Penjualan Hari Ini -->
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Penjualan Hari Ini</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($salesToday, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-500">Dari <span class="font-semibold text-slate-700">{{ $trxTodayCount }} transaksi</span> hari ini</p>
            </div>
        </div>

        <!-- Penjualan Minggu Ini -->
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Penjualan Minggu Ini</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <i class="bi bi-calendar-week text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($salesThisWeek, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-500">Periode minggu berjalan (Senin - Minggu)</p>
            </div>
        </div>

        <!-- Penjualan Bulan Ini -->
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Penjualan Bulan Ini</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="bi bi-graph-up text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-500">Bulan {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>

        <!-- Total Keuntungan -->
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Total Keuntungan</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                    <i class="bi bi-piggy-bank text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-teal-600">Rp {{ number_format($totalProfitAllTime, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-500">Laba bersih (Harga Jual - Harga Beli)</p>
            </div>
        </div>

    </div>

    <!-- BARIS 2: KARTU STATISTIK INVENTARIS & KEDALUWARSA -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <!-- Total Seluruh Barang -->
        <a href="{{ route('admin.products.index') }}" class="group block rounded-xl bg-white p-5 shadow-sm border border-slate-200 hover:border-blue-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Total Barang</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="bi bi-box-seam text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalProducts }}</h3>
                <p class="mt-1 text-xs text-blue-600 flex items-center gap-1 font-medium">Lihat seluruh produk <i class="bi bi-arrow-right"></i></p>
            </div>
        </a>

        <!-- Stok Menipis -->
        <a href="{{ route('admin.products.index', ['stock_filter' => 'low']) }}" class="group block rounded-xl bg-white p-5 shadow-sm border border-slate-200 hover:border-yellow-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Stok Menipis</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-yellow-600' : 'text-slate-800' }}">{{ $lowStockCount }}</h3>
                <p class="mt-1 text-xs text-yellow-600 font-medium">Di bawah batas stok minimum</p>
            </div>
        </a>

        <!-- Barang Kedaluwarsa -->
        <a href="{{ route('admin.expiry.index', ['status' => 'expired']) }}" class="group block rounded-xl bg-white p-5 shadow-sm border border-slate-200 hover:border-red-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Sudah Kedaluwarsa</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <i class="bi bi-x-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold {{ $expiredCount > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $expiredCount }}</h3>
                <p class="mt-1 text-xs text-red-600 font-medium">Wajib dipisahkan & tidak boleh dijual</p>
            </div>
        </a>

        <!-- Mendekati Kedaluwarsa (<= 30 Hari) -->
        <a href="{{ route('admin.expiry.index', ['status' => 'warning_30']) }}" class="group block rounded-xl bg-white p-5 shadow-sm border border-slate-200 hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-slate-500">Mendekati Kedaluwarsa</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-amber-600">{{ $expiringIn30DaysCount }}</h3>
                <p class="mt-1 text-xs text-slate-500"><span class="font-bold text-red-600">{{ $expiringIn7DaysCount }}</span> kritis dalam 7 hari</p>
            </div>
        </a>

    </div>

    <!-- BARIS 3: GRAFIK PENJUALAN CHART.JS & AKSI CEPAT -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Grafik Penjualan (2 Kolom) -->
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-sm border border-slate-200" x-data="{ chartView: 'daily' }">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Tren Penjualan & Keuntungan</h3>
                    <p class="text-xs text-slate-500">Visualisasi pendapatan toko kelontong menggunakan Chart.js</p>
                </div>
                <div class="flex rounded-lg bg-slate-100 p-1">
                    <button @click="chartView = 'daily'; renderDailyChart();"
                            :class="chartView === 'daily' ? 'bg-white shadow text-blue-600 font-semibold' : 'text-slate-600 hover:text-slate-900'"
                            class="rounded-md px-3 py-1 text-xs transition">
                        7 Hari Terakhir
                    </button>
                    <button @click="chartView = 'monthly'; renderMonthlyChart();"
                            :class="chartView === 'monthly' ? 'bg-white shadow text-blue-600 font-semibold' : 'text-slate-600 hover:text-slate-900'"
                            class="rounded-md px-3 py-1 text-xs transition">
                        6 Bulan Terakhir
                    </button>
                </div>
            </div>

            <div class="mt-4 h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Tombol Aksi Cepat & Navigasi -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Aksi Cepat Kasir & Toko</h3>
                <p class="text-xs text-slate-500 mb-4">Pintasan menu penting untuk mempermudah operasional harian</p>
                
                <div class="space-y-3">
                    <a href="{{ route('kasir.transaction') }}"
                       class="flex items-center gap-3.5 p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-600 hover:text-white transition group">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white group-hover:bg-white group-hover:text-blue-600">
                            <i class="bi bi-cash-coin text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Buka Menu Kasir (POS)</p>
                            <p class="text-xs text-blue-600 group-hover:text-blue-100">Layani transaksi penjualan pelanggan</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.products.create') }}"
                       class="flex items-center gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-white">
                            <i class="bi bi-plus-lg text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Tambah Barang Baru</p>
                            <p class="text-xs text-slate-500">Daftarkan produk baru ke katalog toko</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.stock.index') }}"
                       class="flex items-center gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <i class="bi bi-box-arrow-in-down text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Tambah / Koreksi Stok</p>
                            <p class="text-xs text-slate-500">Catat barang masuk dari suplier</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                            <i class="bi bi-file-earmark-spreadsheet text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Laporan Rekap Penjualan</p>
                            <p class="text-xs text-slate-500">Cetak & ekspor rekap mingguan/bulanan</p>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400 text-center">
                Sistem Kasir Toko Kelontong v1.0
            </div>
        </div>

    </div>

    <!-- BARIS 4: TABEL TRANSAKSI TERBARU -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 p-5">
            <div>
                <h3 class="text-base font-bold text-slate-800">Transaksi Penjualan Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar transaksi kasir yang baru saja tercatat di sistem</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
                Lihat Semua <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4 text-right">Total Belanja</th>
                        <th class="py-3 px-4 text-right">Keuntungan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 font-mono font-semibold text-blue-600">{{ $trx->invoice_number }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $trx->user->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">{{ $trx->total_items }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-emerald-600">Rp {{ number_format($trx->total_profit, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.transactions.show', $trx->id) }}" class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-blue-600 hover:text-white transition">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="bi bi-inbox text-3xl mb-1 block"></i>
                                Belum ada transaksi penjualan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const dailyData = {
        labels: {!! json_encode($dailyLabels) !!},
        sales: {!! json_encode($dailySales) !!},
        profits: {!! json_encode($dailyProfits) !!}
    };

    const monthlyData = {
        labels: {!! json_encode($monthlyLabels) !!},
        sales: {!! json_encode($monthlySales) !!},
        profits: {!! json_encode($monthlyProfits) !!}
    };

    let salesChartInstance = null;

    function renderDailyChart() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChartInstance) salesChartInstance.destroy();

        salesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dailyData.labels,
                datasets: [
                    {
                        label: 'Total Penjualan (Rp)',
                        data: dailyData.sales,
                        backgroundColor: 'rgba(37, 99, 235, 0.85)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Keuntungan / Profit (Rp)',
                        data: dailyData.profits,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value >= 1000 ? (value/1000) + 'k' : value);
                            }
                        }
                    }
                }
            }
        });
    }

    function renderMonthlyChart() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChartInstance) salesChartInstance.destroy();

        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.labels,
                datasets: [
                    {
                        label: 'Total Penjualan (Rp)',
                        data: monthlyData.sales,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 5
                    },
                    {
                        label: 'Keuntungan (Rp)',
                        data: monthlyData.profits,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value >= 1000 ? (value/1000) + 'k' : value);
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderDailyChart();
    });
</script>
@endpush

