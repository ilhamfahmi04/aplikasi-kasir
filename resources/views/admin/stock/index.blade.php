@extends('layouts.app')

@section('title', 'Manajemen Stok')
@section('page_title', 'Riwayat & Manajemen Stok Barang')
@section('page_subtitle', 'Pantau seluruh pergerakan barang masuk, barang keluar dari transaksi, dan penyesuaian stok opname')

@section('content')
<div class="space-y-6" x-data="{ addStockModal: false }">

    <!-- BARIS AKSI, FILTER & PENCARIAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="flex flex-wrap items-center gap-2 max-w-xl w-full">
            <div class="relative flex-1 min-w-[200px]">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang atau no referensi..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <select name="type" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 bg-white py-2 px-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua Jenis Gerakan</option>
                <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Stok Masuk (Kulak/Suplier)</option>
                <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Stok Keluar (Penjualan)</option>
                <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Koreksi / Penyesuaian Opname</option>
            </select>

            @if($search || $type)
                <a href="{{ route('admin.stock.index') }}" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <button @click="addStockModal = true"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow hover:bg-blue-700 transition shrink-0">
            <i class="bi bi-box-arrow-in-down"></i> Tambah / Sesuaikan Stok
        </button>
    </div>

    <!-- TABEL RIWAYAT PERGERAKAN STOK -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kode / Barcode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4 text-center">Tipe</th>
                        <th class="py-3 px-4 text-center">Perubahan</th>
                        <th class="py-3 px-4 text-center">Sebelum</th>
                        <th class="py-3 px-4 text-center">Sesudah</th>
                        <th class="py-3 px-4">Referensi / Catatan</th>
                        <th class="py-3 px-4">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-blue-600">{{ $m->product->code ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $m->product->name ?? 'Barang Dihapus' }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($m->type === 'in')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                        <i class="bi bi-arrow-down-left"></i> Masuk
                                    </span>
                                @elseif($m->type === 'out')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700">
                                        <i class="bi bi-arrow-up-right"></i> Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                                        <i class="bi bi-sliders"></i> Penyesuaian
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold">
                                @if($m->type === 'in')
                                    <span class="text-emerald-600">+{{ $m->quantity }}</span>
                                @elseif($m->type === 'out')
                                    <span class="text-red-600">-{{ $m->quantity }}</span>
                                @else
                                    <span class="text-amber-600">{{ $m->quantity }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500">{{ $m->previous_stock }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-800">{{ $m->current_stock }}</td>
                            <td class="py-3 px-4 text-slate-600">
                                <span class="font-medium text-slate-800">{{ $m->reference_number ?: '-' }}</span>
                                @if($m->notes)
                                    <span class="block text-[11px] text-slate-400">{{ $m->notes }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $m->user->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400">
                                <i class="bi bi-arrow-left-right text-3xl mb-1 block"></i>
                                Belum ada riwayat pergerakan stok barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH / SESUAIKAN STOK -->
    <div x-show="addStockModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="addStockModal = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800">Formulir Tambah / Penyesuaian Stok</h3>
                <button @click="addStockModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.stock.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                    <select name="product_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Barang Toko --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Stok saat ini: {{ $p->stock }} {{ $p->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Tindakan <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="in">Stok Masuk (Kulak / Suplier)</option>
                            <option value="out">Stok Keluar Manual (Rusak / Hilang)</option>
                            <option value="adjustment">Koreksi Opname (Ubah jadi nilai tetap)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Kuantitas <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" required placeholder="Contoh: 10"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Referensi / Surat Jalan (Opsional)</label>
                    <input type="text" name="reference_number" placeholder="Contoh: FAKTUR-2026/10/01"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Alasan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Pembelian tambahan dari agen sembako"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="addStockModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Perubahan Stok</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

