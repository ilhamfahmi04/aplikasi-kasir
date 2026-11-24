<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $today = Carbon::today()->toDateString();
        $in7Days = Carbon::today()->addDays(7)->toDateString();
        $in30Days = Carbon::today()->addDays(30)->toDateString();

        $query = Product::with('category')->whereNotNull('expired_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status === 'expired') {
            $query->where('expired_date', '<', $today);
        } elseif ($status === 'critical_7') {
            $query->whereBetween('expired_date', [$today, $in7Days]);
        } elseif ($status === 'warning_30') {
            $query->whereBetween('expired_date', [$today, $in30Days]);
        }

        $products = $query->orderBy('expired_date', 'asc')->paginate(12)->withQueryString();

        // Ringkasan angka
        $expiredCount = Product::whereNotNull('expired_date')->where('expired_date', '<', $today)->count();
        $critical7Count = Product::whereNotNull('expired_date')->whereBetween('expired_date', [$today, $in7Days])->count();
        $warning30Count = Product::whereNotNull('expired_date')->whereBetween('expired_date', [$today, $in30Days])->count();

        return view('admin.expiry.index', compact('products', 'status', 'search', 'expiredCount', 'critical7Count', 'warning30Count'));
    }

    /**
     * Memperbarui tanggal kedaluwarsa produk (misal ada batch baru dari suplier).
     */
    public function updateExpiry(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'expired_date' => ['required', 'date'],
            'add_stock' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'expired_date.required' => 'Tanggal kedaluwarsa baru wajib diisi.',
            'add_stock.min' => 'Penambahan stok tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            $addStock = (int) ($validated['add_stock'] ?? 0);
            $prevStock = $product->stock;

            if ($addStock > 0) {
                $newStock = $prevStock + $addStock;
                $product->stock = $newStock;

                // Catat pergerakan stok masuk untuk batch baru
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $addStock,
                    'previous_stock' => $prevStock,
                    'current_stock' => $newStock,
                    'reference_number' => 'BATCH-BARU-' . time(),
                    'notes' => $validated['notes'] ?: 'Pembaruan tanggal kedaluwarsa dan penambahan stok batch baru',
                ]);
            }

            $product->expired_date = $validated['expired_date'];
            $product->save();
        });

        return redirect()->route('admin.expiry.index')->with('success', "Tanggal kedaluwarsa untuk '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Memusnahkan / mengosongkan sisa stok barang yang kedaluwarsa.
     */
    public function disposeStock(Request $request, Product $product): RedirectResponse
    {
        if ($product->stock <= 0) {
            return redirect()->route('admin.expiry.index')->with('error', "Stok barang '{$product->name}' sudah kosong (0).");
        }

        $oldStock = $product->stock;

        DB::transaction(function () use ($request, $product, $oldStock) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => StockMovement::TYPE_OUT,
                'quantity' => $oldStock,
                'previous_stock' => $oldStock,
                'current_stock' => 0,
                'reference_number' => 'PEMUSNAHAN-EXP-' . time(),
                'notes' => 'Pemusnahan/retur barang kedaluwarsa oleh admin',
            ]);

            $product->update(['stock' => 0]);
        });

        return redirect()->route('admin.expiry.index')->with('success', "Stok barang kedaluwarsa '{$product->name}' sebanyak {$oldStock} {$product->unit} berhasil dimusnahkan dan dicatat ke riwayat stok keluar.");
    }

    /**
     * Menghapus barang kedaluwarsa secara permanen.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.expiry.index')->with('success', "Barang '{$name}' berhasil dihapus dari sistem.");
    }
}
