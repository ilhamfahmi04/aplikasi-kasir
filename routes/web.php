<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpiryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\KasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Kasir Toko Kelontong
|--------------------------------------------------------------------------
*/

// Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Rute Autentikasi Umum (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pengalihan halaman utama berdasarkan peran akun
    Route::get('/', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('kasir.dashboard');
    })->name('dashboard');
});

// GRUP RUTE ADMIN (Hanya Role Admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Kategori Barang
    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 3. Manajemen Barang
    Route::get('/barang', [ProductController::class, 'index'])->name('products.index');
    Route::get('/barang/tambah', [ProductController::class, 'create'])->name('products.create');
    Route::post('/barang', [ProductController::class, 'store'])->name('products.store');
    Route::get('/barang/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/barang/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/barang/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // 4. Manajemen Stok
    Route::get('/stok', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stok', [StockController::class, 'store'])->name('stock.store');

    // 5. Monitoring Kedaluwarsa
    Route::get('/kedaluwarsa', [ExpiryController::class, 'index'])->name('expiry.index');
    Route::put('/kedaluwarsa/{product}', [ExpiryController::class, 'updateExpiry'])->name('expiry.update');
    Route::post('/kedaluwarsa/{product}/musnahkan', [ExpiryController::class, 'disposeStock'])->name('expiry.dispose');
    Route::delete('/kedaluwarsa/{product}', [ExpiryController::class, 'destroy'])->name('expiry.destroy');

    // 6. Riwayat Transaksi Seluruh Toko
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // 7. Laporan Penjualan (Rekap Mingguan & Bulanan)
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/cetak', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/laporan/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // 8. Manajemen Pengguna (Kasir & Admin)
    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// GRUP RUTE KASIR (Role Kasir & Admin juga dapat mengakses POS)
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir,admin'])->group(function () {
    // Dashboard Kasir
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

    // Halaman Transaksi POS Kasir
    Route::get('/transaksi', [KasirController::class, 'transaction'])->name('transaction');

    // API Transaksi Kasir
    Route::get('/api/products', [KasirController::class, 'searchProducts'])->name('api.products');
    Route::post('/api/transaksi', [KasirController::class, 'storeTransaction'])->name('api.transaction.store');

    // Cetak Struk Transaksi
    Route::get('/struk/{transaction}', [KasirController::class, 'printReceipt'])->name('print');

    // Riwayat Transaksi Kasir Pribadi
    Route::get('/riwayat', [KasirController::class, 'history'])->name('history');
});
