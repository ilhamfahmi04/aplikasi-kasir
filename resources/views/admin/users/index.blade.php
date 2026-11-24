@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Akun Pengguna')
@section('page_subtitle', 'Kelola akun kasir dan hak akses staf toko kelontong')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editUser: { id: null, name: '', email: '', role: 'kasir', is_active: 1 } }">

    <!-- BARIS AKSI & PENCARIAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 max-w-sm w-full">
            <div class="relative w-full">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email pengguna..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            @if($search)
                <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <button @click="createModal = true" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-blue-700 transition">
            <i class="bi bi-person-plus"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- TABEL PENGGUNA -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email Login</th>
                        <th class="py-3 px-4 text-center">Peran (Role)</th>
                        <th class="py-3 px-4 text-center">Status Akun</th>
                        <th class="py-3 px-4">Dibuat Pada</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3 px-4 text-center text-slate-500">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $u->name }}</td>
                            <td class="py-3 px-4 text-slate-600 font-mono">{{ $u->email }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-bold text-blue-800 uppercase">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 uppercase">
                                        Kasir
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-semibold text-red-600">
                                        <i class="bi bi-slash-circle text-[10px]"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button @click="editUser = { id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', email: '{{ addslashes($u->email) }}', role: '{{ $u->role }}', is_active: {{ $u->is_active ? 1 : 0 }} }; editModal = true;"
                                            class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit Pengguna">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <form id="delete-user-{{ $u->id }}" method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmDelete('delete-user-{{ $u->id }}', 'pengguna {{ addslashes($u->name) }}')"
                                                    class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-red-50 hover:text-red-600 transition" title="Hapus Pengguna">
                                                <i class="bi bi-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data pengguna yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH PENGGUNA -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="createModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800">Tambah Akun Pengguna Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Siti Rahma"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Login <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="kasir2@kelontong.com"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Login <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran Akses (Role) <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                        <option value="kasir">Kasir (Hanya Akses POS Transaksi & Riwayat)</option>
                        <option value="admin">Admin (Akses Penuh Manajemen Toko)</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="create_active" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600">
                    <label for="create_active" class="text-xs font-semibold text-slate-700">Akun Langsung Aktif</label>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="createModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENGGUNA -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="editModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800">Edit Akun Pengguna</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <form :action="'{{ url('admin/pengguna') }}/' + editUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="editUser.name" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Login <span class="text-red-500">*</span></label>
                    <input type="email" name="email" x-model="editUser.email" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tetap"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran Akses (Role) <span class="text-red-500">*</span></label>
                    <select name="role" x-model="editUser.role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_active" name="is_active" value="1" :checked="editUser.is_active == 1" class="rounded border-slate-300 text-blue-600">
                    <label for="edit_active" class="text-xs font-semibold text-slate-700">Akun Aktif (Dapat Login)</label>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="editModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-semibold text-white hover:bg-blue-700">Perbarui Pengguna</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

