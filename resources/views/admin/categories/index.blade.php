@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page_title', 'Manajemen Kategori Barang')
@section('page_subtitle', 'Kelola kelompok barang toko untuk mempermudah pencarian dan pengelompokan')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editCategory: { id: null, name: '', description: '' } }">

    <!-- BARIS AKSI & PENCARIAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-center gap-2 max-w-sm w-full">
            <div class="relative w-full">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-4 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            @if($search)
                <a href="{{ route('admin.categories.index') }}" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <button @click="createModal = true" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-blue-700 transition">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </button>
    </div>

    <!-- TABEL KATEGORI -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 text-center text-slate-500">{{ $categories->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $cat->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $cat->description ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                    {{ $cat->products_count }} Barang
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button @click="editCategory = { id: {{ $cat->id }}, name: '{{ addslashes($cat->name) }}', description: '{{ addslashes($cat->description ?? '') }}' }; editModal = true;"
                                            class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </button>

                                    <form id="delete-cat-{{ $cat->id }}" method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-cat-{{ $cat->id }}', 'kategori {{ addslashes($cat->name) }}')"
                                                class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-red-50 hover:text-red-600 transition" title="Hapus">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <i class="bi bi-tags text-3xl mb-1 block"></i>
                                Belum ada kategori barang yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="createModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800">Tambah Kategori Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Sembako, Minuman, Obat"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Keterangan singkat kategori..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="createModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="editModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800">Edit Kategori</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <form :action="'{{ url('admin/kategori') }}/' + editCategory.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="editCategory.name" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" x-model="editCategory.description"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="editModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

