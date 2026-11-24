@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page_title', 'Tambah Barang Baru')
@section('page_subtitle', 'Masukkan informasi produk kelontong ke dalam database inventaris toko')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-6 md:p-8">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <h3 class="text-sm font-bold text-slate-800">Formulir Tambah Produk</h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-5" x-data="{
            generateCode() {
                const random = Math.floor(100000000000 + Math.random() * 900000000000);
                this.$refs.codeInput.value = random;
            }
        }">
            @csrf

            <!-- Barcode & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Kode Barang / Barcode <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="code" x-ref="codeInput" value="{{ old('code') }}" required autofocus
                               placeholder="Contoh: 899123456789"
                               class="w-full rounded-lg border {{ $errors->has('code') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs font-mono focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="button" @click="generateCode()" title="Buat Barcode Acak Otomatis"
                                class="shrink-0 rounded-lg bg-slate-100 border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                            Acak
                        </button>
                    </div>
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
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Minyak Goreng Sawit 2 Liter, Indomie Goreng 85g"
                       class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli Modal & Harga Jual -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Harga Beli Modal (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('buy_price') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Harga pokok modal per satuan</span>
                    @error('buy_price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Harga Jual Konsumen (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', 0) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('sell_price') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Harga jual ke pembeli di kasir</span>
                    @error('sell_price')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stok Awal, Batas Minimum Stok & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Stok Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock" value="{{ old('stock', 10) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('stock') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Batas Stok Minimum <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required
                           class="w-full rounded-lg border {{ $errors->has('min_stock') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Peringatan saat stok mencapai batas ini</span>
                    @error('min_stock')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Satuan Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="unit" list="unit-list" value="{{ old('unit', 'pcs') }}" required
                           class="w-full rounded-lg border {{ $errors->has('unit') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <datalist id="unit-list">
                        <option value="pcs">
                        <option value="kg">
                        <option value="liter">
                        <option value="bks">
                        <option value="dus">
                        <option value="botol">
                        <option value="kaleng">
                        <option value="strip">
                        <option value="pack">
                    </datalist>
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
                <input type="date" name="expired_date" value="{{ old('expired_date') }}"
                       class="w-full sm:w-1/2 rounded-lg border {{ $errors->has('expired_date') ? 'border-red-400' : 'border-slate-300' }} px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <p class="mt-1 text-[11px] text-slate-400">Kosongkan jika produk tidak memiliki masa kedaluwarsa (misal sabun cuci piring, kantong plastik).</p>
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
                    Simpan Barang Baru
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

