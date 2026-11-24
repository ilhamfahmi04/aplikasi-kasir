@extends('layouts.app')

@section('title', 'Edit Barang')
@section('page_title', 'Edit Data Barang')
@section('page_subtitle', 'Perbarui rincian informasi produk, harga jual, dan tanggal kedaluwarsa')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-6 md:p-8">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Edit: {{ $product->name }}</h3>
                <span class="text-xs font-mono text-blue-600">Barcode: {{ $product->code }}</span>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Barcode & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Kode Barang / Barcode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" required
                           class="w-full rounded-lg border {{ $errors->has('code') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs font-mono focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('code')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Kategori Barang <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full rounded-lg border {{ $errors->has('category_id') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama Barang -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Barang Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli & Harga Jual -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Harga Beli Modal (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', (int)$product->buy_price) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('buy_price') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Harga beli/kulak per satuan</span>
                    @error('buy_price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Harga Jual Konsumen (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', (int)$product->sell_price) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('sell_price') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Harga jual ke konsumen</span>
                    @error('sell_price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Batas Stok Minimum & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Stok Saat Ini (Terkunci)
                    </label>
                    <input type="text" value="{{ $product->stock }} {{ $product->unit }}" disabled
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">
                    <span class="text-[11px] text-blue-600">Ubah stok lewat menu Riwayat & Tambah Stok</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Batas Stok Minimum <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('min_stock') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('min_stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Satuan Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                           class="w-full rounded-lg border {{ $errors->has('unit') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('unit')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal Kedaluwarsa -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Tanggal Kedaluwarsa (Expired Date)
                </label>
                <input type="date" name="expired_date" value="{{ old('expired_date', $product->expired_date ? $product->expired_date->format('Y-m-d') : '') }}"
                       class="w-full sm:w-1/2 rounded-lg border {{ $errors->has('expired_date') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                @error('expired_date')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.products.index') }}" class="rounded-lg bg-slate-100 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-xs font-semibold text-white shadow hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

