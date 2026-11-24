<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockFilter = $request->query('stock_filter');

        $query = Product::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockFilter === 'low') {
            $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
        } elseif ($stockFilter === 'empty') {
            $query->where('stock', '<=', 0);
        } elseif ($stockFilter === 'available') {
            $query->whereColumn('stock', '>', 'min_stock');
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId', 'stockFilter'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'expired_date' => ['nullable', 'date'],
        ], [
            'code.required' => 'Kode barang / Barcode wajib diisi.',
            'code.unique' => 'Kode barang / Barcode ini sudah digunakan.',
            'name.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori barang harus dipilih.',
            'buy_price.min' => 'Harga beli tidak boleh bernilai negatif.',
            'sell_price.min' => 'Harga jual tidak boleh bernilai negatif.',
            'stock.min' => 'Stok barang tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            // Jika stok awal > 0, catat riwayat stok masuk
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $product->stock,
                    'previous_stock' => 0,
                    'current_stock' => $product->stock,
                    'reference_number' => 'STOK-AWAL-' . $product->code,
                    'notes' => 'Pencatatan stok awal saat penambahan barang',
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:products,code,' . $product->id],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'expired_date' => ['nullable', 'date'],
        ], [
            'code.required' => 'Kode barang / Barcode wajib diisi.',
            'code.unique' => 'Kode barang / Barcode ini sudah digunakan barang lain.',
            'name.required' => 'Nama barang wajib diisi.',
            'buy_price.min' => 'Harga beli tidak boleh negatif.',
            'sell_price.min' => 'Harga jual tidak boleh negatif.',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Cek apakah barang sudah pernah memiliki transaksi penjualan
        if ($product->transactionDetails()->count() > 0) {
            return redirect()->route('admin.products.index')->with('error', 'Barang tidak dapat dihapus karena tercatat dalam data riwayat transaksi penjualan toko.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Barang berhasil dihapus.');
    }
}

