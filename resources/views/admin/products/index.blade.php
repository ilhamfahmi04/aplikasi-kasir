@extends('layouts.app')

@section('title', 'Data Barang')
@section('page_title', 'Manajemen Data Barang')
@section('page_subtitle', 'Kelola daftar katalog produk, harga jual, harga modal, dan status ketersediaan barang')

@section('content')
<div class="space-y-6">

    <!-- BARIS FILTER, PENCARIAN & TOMBOL TAMBAH -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        
        <!-- Formulir Filter & Pencarian -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 flex-1 max-w-3xl">
            <!-- Search -->
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau barcode..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Filter Kategori -->
            <select name="category_id" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 bg-white py-2 px-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Filter Stok -->
            <select name="stock_filter" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 bg-white py-2 px-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua Status Stok</option>
                <option value="available" {{ $stockFilter === 'available' ? 'selected' : '' }}>Stok Aman / Tersedia</option>
                <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>Stok Menipis (<= Batas Min)</option>
                <option value="empty" {{ $stockFilter === 'empty' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>
        </form>

        <!-- Tombol Tambah Barang -->
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow hover:bg-blue-700 transition shrink-0">
            <i class="bi bi-plus-lg"></i> Tambah Barang Baru
        </a>

    </div>

    <!-- TABEL DATA BARANG -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode / Barcode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Beli</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-center">Kedaluwarsa</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $index => $item)
                        @php
                            $expiryStatus = $item->expiry_status;
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition {{ $item->isExpired() ? 'bg-red-50/30' : '' }}">
                            <td class="py-3 px-4 text-center text-slate-500">{{ $products->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-blue-600">{{ $item->code }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800">{{ $item->name }}</span>
                                <span class="block text-[11px] text-slate-400">Satuan: {{ $item->unit }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                    {{ $item->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800">Rp {{ number_format($item->sell_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($item->stock <= 0)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-bold text-red-700">
                                        Habis (0)
                                    </span>
                                @elseif($item->isLowStock())
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-[11px] font-bold text-yellow-800" title="Batas min: {{ $item->min_stock }}">
                                        {{ $item->stock }} {{ $item->unit }} ⚠️
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                                        {{ $item->stock }} {{ $item->unit }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->expired_date)
                                    @if($expiryStatus === 'expired')
                                        <span class="inline-flex items-center gap-1 rounded bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-700">
                                            <i class="bi bi-x-circle-fill"></i> Kedaluwarsa ({{ $item->expired_date->format('d/m/Y') }})
                                        </span>
                                    @elseif($expiryStatus === 'critical_7')
                                        <span class="inline-flex items-center gap-1 rounded bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600" title="{{ $item->days_until_expiration }} hari lagi">
                                            <i class="bi bi-exclamation-circle-fill"></i> {{ $item->expired_date->format('d/m/Y') }} ({{ $item->days_until_expiration }}h)
                                        </span>
                                    @elseif($expiryStatus === 'warning_30')
                                        <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700" title="{{ $item->days_until_expiration }} hari lagi">
                                            <i class="bi bi-clock"></i> {{ $item->expired_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-slate-600">{{ $item->expired_date->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.products.edit', $item->id) }}"
                                       class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <form id="delete-prod-{{ $item->id }}" method="POST" action="{{ route('admin.products.destroy', $item->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-prod-{{ $item->id }}', 'barang {{ addslashes($item->name) }}')"
                                                class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-red-50 hover:text-red-600 transition" title="Hapus">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400">
                                <i class="bi bi-box-seam text-3xl mb-1 block"></i>
                                Tidak ada data barang yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

