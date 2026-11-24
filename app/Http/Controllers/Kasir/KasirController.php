<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasirController extends Controller
{
    /**
     * Dashboard ringkasan untuk kasir.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $today = Carbon::today();

        $mySalesToday = Transaction::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $myTrxTodayCount = Transaction::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        return view('kasir.dashboard', compact('mySalesToday', 'myTrxTodayCount', 'recentTransactions'));
    }

    /**
     * Halaman Kasir / Point of Sale (POS).
     */
    public function transaction(): View
    {
        return view('kasir.transaction');
    }

    /**
     * API pencarian produk untuk kasir (Live search & Barcode scan).
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([]);
        }

        $products = Product::with('category')
            ->where(function ($q) use ($query) {
                $q->where('code', $query)
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'category' => $item->category->name ?? '-',
                    'sell_price' => (float) $item->sell_price,
                    'buy_price' => (float) $item->buy_price,
                    'stock' => $item->stock,
                    'unit' => $item->unit,
                    'is_expired' => $item->isExpired(),
                    'expired_date' => $item->expired_date ? $item->expired_date->format('d/m/Y') : null,
                ];
            });

        return response()->json($products);
    }

    /**
     * Simpan transaksi penjualan kasir menggunakan Database Transaction.
     */
    public function storeTransaction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'cash_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang belanja masih kosong.',
            'cash_amount.required' => 'Jumlah uang yang dibayarkan pelanggan harus diisi.',
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated, $request) {
                $user = $request->user();
                $items = $validated['items'];
                $cashAmount = (float) $validated['cash_amount'];

                $totalAmount = 0;
                $totalCost = 0;
                $totalItemsCount = 0;
                $detailsToInsert = [];
                $stockUpdates = [];

                // 1. Validasi setiap barang di keranjang
                foreach ($items as $itemData) {
                    // Lock baris produk untuk mencegah race condition stok
                    $product = Product::lockForUpdate()->find($itemData['id']);

                    if (!$product) {
                        throw new \Exception("Barang dengan ID {$itemData['id']} tidak ditemukan.");
                    }

                    // Pencegahan penjualan barang kedaluwarsa
                    if ($product->isExpired()) {
                        throw new \Exception("Barang '{$product->name}' sudah kedaluwarsa dan tidak boleh dijual!");
                    }

                    // Pencegahan stok tidak mencukupi
                    $qty = (int) $itemData['quantity'];
                    if ($product->stock < $qty) {
                        throw new \Exception("Stok barang '{$product->name}' tidak mencukupi! Sisa stok: {$product->stock} {$product->unit}, diminta: {$qty}.");
                    }

                    $subtotal = $qty * (float) $product->sell_price;
                    $cost = $qty * (float) $product->buy_price;
                    $profit = $subtotal - $cost;

                    $totalAmount += $subtotal;
                    $totalCost += $cost;
                    $totalItemsCount += $qty;

                    $detailsToInsert[] = [
                        'product_id' => $product->id,
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'buy_price' => $product->buy_price,
                        'sell_price' => $product->sell_price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                        'subtotal_profit' => $profit,
                    ];

                    $stockUpdates[] = [
                        'product' => $product,
                        'qty' => $qty,
                        'prev_stock' => $product->stock,
                        'new_stock' => $product->stock - $qty,
                    ];
                }

                // Validasi kecukupan uang tunai
                if ($cashAmount < $totalAmount) {
                    throw new \Exception("Uang tunai yang diterima (Rp " . number_format($cashAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalAmount, 0, ',', '.') . ").");
                }

                $changeAmount = $cashAmount - $totalAmount;
                $totalProfit = $totalAmount - $totalCost;

                // 2. Generate Nomor Invoice Unik (TRX-YYYYMMDD-XXXX)
                $datePrefix = 'TRX-' . now()->format('Ymd');
                $lastTrxToday = Transaction::where('invoice_number', 'like', "{$datePrefix}-%")
                    ->latest('id')
                    ->first();

                $nextNumber = 1;
                if ($lastTrxToday) {
                    $parts = explode('-', $lastTrxToday->invoice_number);
                    $nextNumber = ((int) end($parts)) + 1;
                }
                $invoiceNumber = sprintf('%s-%04d', $datePrefix, $nextNumber);

                // 3. Simpan Header Transaksi
                $transaction = Transaction::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => $user->id,
                    'total_items' => $totalItemsCount,
                    'total_amount' => $totalAmount,
                    'total_cost' => $totalCost,
                    'total_profit' => $totalProfit,
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // 4. Simpan Detail Transaksi & Kurangi Stok
                foreach ($detailsToInsert as $detail) {
                    $detail['transaction_id'] = $transaction->id;
                    $detail['created_at'] = now();
                    $detail['updated_at'] = now();
                    TransactionDetail::create($detail);
                }

                foreach ($stockUpdates as $update) {
                    $prod = $update['product'];
                    $prod->update(['stock' => $update['new_stock']]);

                    // Catat ke Riwayat Stok Keluar
                    StockMovement::create([
                        'product_id' => $prod->id,
                        'user_id' => $user->id,
                        'type' => StockMovement::TYPE_OUT,
                        'quantity' => $update['qty'],
                        'previous_stock' => $update['prev_stock'],
                        'current_stock' => $update['new_stock'],
                        'reference_number' => $invoiceNumber,
                        'notes' => "Penjualan kasir transaksi {$invoiceNumber}",
                    ]);
                }

                return $transaction;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'print_url' => route('kasir.print', $transaction->id),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Tampilan Cetak Struk Pembelian (Thermal / Standar).
     */
    public function printReceipt(Transaction $transaction): View
    {
        $transaction->load(['user', 'details']);
        return view('kasir.receipt', compact('transaction'));
    }

    /**
     * Riwayat transaksi kasir yang sedang login.
     */
    public function history(Request $request): View
    {
        $search = $request->query('search');
        $date = $request->query('date');

        $transactions = Transaction::where('user_id', $request->user()->id)
            ->when($search, function ($q, $search) {
                $q->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($date, function ($q, $date) {
                $q->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kasir.history', compact('transactions', 'search', 'date'));
    }
}

