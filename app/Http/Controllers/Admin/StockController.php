<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $movements = StockMovement::with(['product', 'user'])
            ->when($type, function ($q, $type) {
                $q->where('type', $type);
            })
            ->when($search, function ($q, $search) {
                $q->whereHas('product', function ($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                })->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('admin.stock.index', compact('movements', 'products', 'type', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'product_id.required' => 'Pilih barang yang ingin disesuaikan stoknya.',
            'quantity.min' => 'Jumlah kuantitas minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $prevStock = $product->stock;
            $qty = $validated['quantity'];

            if ($validated['type'] === StockMovement::TYPE_IN) {
                $newStock = $prevStock + $qty;
            } elseif ($validated['type'] === StockMovement::TYPE_OUT) {
                if ($prevStock < $qty) {
                    throw new \Exception("Stok tidak mencukupi untuk dikeluarkan. Stok saat ini: {$prevStock}");
                }
                $newStock = $prevStock - $qty;
            } else { // adjustment
                $newStock = $qty; // jika tipe adjustment, isi qty menjadi stok baru
                $qty = abs($newStock - $prevStock);
            }

            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $validated['type'] === 'adjustment' ? $qty : $validated['quantity'],
                'previous_stock' => $prevStock,
                'current_stock' => $newStock,
                'reference_number' => $validated['reference_number'] ?: ('MANUAL-' . strtoupper($validated['type']) . '-' . time()),
                'notes' => $validated['notes'] ?: 'Penyesuaian stok manual oleh admin',
            ]);
        });

        return redirect()->route('admin.stock.index')->with('success', 'Perubahan stok berhasil dicatat dan stok barang telah diperbarui.');
    }
}

