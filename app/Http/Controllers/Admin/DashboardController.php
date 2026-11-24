<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Statistik Penjualan & Transaksi
        $salesToday = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $trxTodayCount = Transaction::whereDate('created_at', $today)->count();

        $salesThisWeek = Transaction::where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $salesThisMonth = Transaction::where('created_at', '>=', $startOfMonth)->sum('total_amount');
        $totalProfitAllTime = Transaction::sum('total_profit');

        // 2. Statistik Barang & Stok
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // 3. Statistik Kedaluwarsa
        $expiredCount = Product::whereNotNull('expired_date')
            ->where('expired_date', '<', $today->toDateString())
            ->count();

        $expiringIn30DaysCount = Product::whereNotNull('expired_date')
            ->whereBetween('expired_date', [$today->toDateString(), $today->copy()->addDays(30)->toDateString()])
            ->count();

        $expiringIn7DaysCount = Product::whereNotNull('expired_date')
            ->whereBetween('expired_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->count();

        // 4. Data Transaksi Terbaru
        $recentTransactions = Transaction::with('user')
            ->latest()
            ->take(6)
            ->get();

        // 5. Data Grafik Penjualan (7 Hari Terakhir)
        $dailyLabels = [];
        $dailySales = [];
        $dailyProfits = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyLabels[] = $date->translatedFormat('D, d M');
            
            $dayTransactions = Transaction::whereDate('created_at', $date)->get();
            $dailySales[] = (float) $dayTransactions->sum('total_amount');
            $dailyProfits[] = (float) $dayTransactions->sum('total_profit');
        }

        // 6. Data Grafik Bulanan (6 Bulan Terakhir)
        $monthlyLabels = [];
        $monthlySales = [];
        $monthlyProfits = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $monthlyLabels[] = $month->translatedFormat('F Y');

            $monthTransactions = Transaction::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->get();
            $monthlySales[] = (float) $monthTransactions->sum('total_amount');
            $monthlyProfits[] = (float) $monthTransactions->sum('total_profit');
        }

        return view('admin.dashboard', compact(
            'salesToday',
            'trxTodayCount',
            'salesThisWeek',
            'salesThisMonth',
            'totalProfitAllTime',
            'totalProducts',
            'lowStockCount',
            'expiredCount',
            'expiringIn30DaysCount',
            'expiringIn7DaysCount',
            'recentTransactions',
            'dailyLabels',
            'dailySales',
            'dailyProfits',
            'monthlyLabels',
            'monthlySales',
            'monthlyProfits'
        ));
    }
}

