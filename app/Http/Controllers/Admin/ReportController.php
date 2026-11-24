<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $periodType = $request->query('period_type', 'monthly'); // 'weekly', 'monthly', 'custom'
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        if ($periodType === 'weekly') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();
        } elseif ($periodType === 'monthly') {
            $carbonMonth = Carbon::createFromFormat('Y-m', $selectedMonth);
            $start = $carbonMonth->copy()->startOfMonth();
            $end = $carbonMonth->copy()->endOfMonth();
        } else { // custom
            $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        }

        $query = Transaction::whereBetween('created_at', [$start, $end]);

        $totalTransactions = (clone $query)->count();
        $totalItemsSold = (clone $query)->sum('total_items');
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalCost = (clone $query)->sum('total_cost');
        $totalProfit = (clone $query)->sum('total_profit');

        // Produk Paling Laris (Top 5)
        $topProducts = TransactionDetail::select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('SUM(subtotal_profit) as total_profit')
            )
            ->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Tanggal dengan Penjualan Tertinggi
        $highestDay = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_trx'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('total_sales')
            ->first();

        // Data transaksi rinci
        $transactions = (clone $query)->with(['user', 'details'])->latest()->get();

        return view('admin.reports.index', compact(
            'periodType',
            'start',
            'end',
            'selectedMonth',
            'totalTransactions',
            'totalItemsSold',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'topProducts',
            'highestDay',
            'transactions'
        ));
    }

    public function print(Request $request): View
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));

        $query = Transaction::whereBetween('created_at', [$start, $end]);

        $totalTransactions = (clone $query)->count();
        $totalItemsSold = (clone $query)->sum('total_items');
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalCost = (clone $query)->sum('total_cost');
        $totalProfit = (clone $query)->sum('total_profit');

        $transactions = (clone $query)->with(['user', 'details'])->latest()->get();

        return view('admin.reports.print', compact(
            'start',
            'end',
            'totalTransactions',
            'totalItemsSold',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'transactions'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));

        $transactions = Transaction::with(['user', 'details'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $filename = "Laporan-Penjualan-{$start->format('Ymd')}-sampai-{$end->format('Ymd')}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            // Menulis BOM UTF-8 agar Excel membuka teks dengan benar
            fputs($file, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($file, [
                'No. Invoice',
                'Tanggal & Waktu',
                'Kasir',
                'Jumlah Item',
                'Total Omset (Rp)',
                'Total Modal (Rp)',
                'Keuntungan (Rp)',
                'Uang Diterima (Rp)',
                'Kembalian (Rp)',
            ]);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->invoice_number,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->user->name ?? '-',
                    $t->total_items,
                    $t->total_amount,
                    $t->total_cost,
                    $t->total_profit,
                    $t->cash_amount,
                    $t->change_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

