@extends('layouts.app')

@section('title', 'Monitoring Kedaluwarsa')
@section('page_title', 'Monitoring Barang Kedaluwarsa')
@section('page_subtitle', 'Sistem pengawasan tanggal kedaluwarsa, pembaruan tanggal simpan, dan pemusnahan barang kedaluwarsa')

@section('content')
<div class="space-y-6" x-data="{ 
    updateModal: false, 
    activeProduct: { id: null, name: '', code: '', stock: 0, unit: '', expired_date: '' } 
}">

    <!-- KARTU STATUS KEDALUWARSA -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Sudah Kedaluwarsa -->
        <a href="{{ route('admin.expiry.index', ['status' => 'expired']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'expired' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-slate-200' }} hover:border-red-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-red-600">Sudah Kedaluwarsa</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <i class="bi bi-x-circle-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-red-600">{{ $expiredCount }}</h3>
                <p class="mt-1 text-xs text-red-700 font-medium">DILARANG DIJUAL - Segera buang/retur</p>
            </div>
        </a>

        <!-- Kritis <= 7 Hari -->
        <a href="{{ route('admin.expiry.index', ['status' => 'critical_7']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'critical_7' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200' }} hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-amber-600">Kritis (≤ 7 Hari)</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-amber-600">{{ $critical7Count }}</h3>
                <p class="mt-1 text-xs text-amber-700 font-medium">Beri diskon cuci gudang / pindah ke depan</p>
            </div>
        </a>

        <!-- Peringatan <= 30 Hari -->
        <a href="{{ route('admin.expiry.index', ['status' => 'warning_30']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border {{ $status === 'warning_30' ? 'border-yellow-500 ring-2 ring-yellow-500/20' : 'border-slate-200' }} hover:border-yellow-400 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-yellow-700">Peringatan (≤ 30 Hari)</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-yellow-700">{{ $warning30Count }}</h3>
                <p class="mt-1 text-xs text-yellow-800 font-medium">Pantau perputaran stok barang</p>
            </div>
        </a>

    </div>

    <!-- BARIS FILTER & PENCARIAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.expiry.index') }}" class="flex items-center gap-2 max-w-md w-full">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang atau barcode..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            @if($search)
                <a href="{{ route('admin.expiry.index', ['status' => $status]) }}" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-1.5 bg-slate-200 p-1 rounded-lg text-xs">
            <a href="{{ route('admin.expiry.index', ['status' => 'all']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Semua
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'expired']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'expired' ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Kedaluwarsa
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'critical_7']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'critical_7' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ≤ 7 Hari
            </a>
            <a href="{{ route('admin.expiry.index', ['status' => 'warning_30']) }}"
               class="px-3 py-1.5 rounded-md font-semibold transition {{ $status === 'warning_30' ? 'bg-yellow-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ≤ 30 Hari
            </a>
        </div>
    </div>

    <!-- TABEL MONITORING KEDALUWARSA DENGAN OPSI AKSI -->
    <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Kode Barcode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-center">Tanggal Kedaluwarsa</th>
                        <th class="py-3 px-4 text-center">Sisa Waktu</th>
                        <th class="py-3 px-4 text-center">Status & Rekomendasi</th>
                        <th class="py-3 px-4 text-center w-36">Aksi & Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        @php
                            $days = $p->days_until_expiration;
                            $expStatus = $p->expiry_status;
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition {{ $expStatus === 'expired' ? 'bg-red-50/40' : ($expStatus === 'critical_7' ? 'bg-amber-50/40' : '') }}">
                            <td class="py-3 px-4 font-mono font-semibold text-blue-600">{{ $p->code }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $p->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $p->category->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                @if($p->stock <= 0)
                                    <span class="text-slate-400">0 {{ $p->unit }}</span>
                                @else
                                    {{ $p->stock }} {{ $p->unit }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-700">
                                {{ $p->expired_date ? $p->expired_date->format('d F Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($days < 0)
                                    <span class="font-bold text-red-600">Lewat {{ abs($days) }} hari lalu</span>
                                @elseif($days === 0)
                                    <span class="font-bold text-red-600">Hari ini!</span>
                                @else
                                    <span class="font-semibold {{ $days <= 7 ? 'text-amber-600' : 'text-slate-600' }}">{{ $days }} hari lagi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($expStatus === 'expired')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-[11px] font-bold text-red-700">
                                        <i class="bi bi-x-octagon-fill"></i> DILARANG DIJUAL (Kedaluwarsa)
                                    </span>
                                @elseif($expStatus === 'critical_7')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-[11px] font-bold text-red-600">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Sangat Mendesak (≤ 7 Hari)
                                    </span>
                                @elseif($expStatus === 'warning_30')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-[11px] font-semibold text-amber-700">
                                        <i class="bi bi-clock-fill"></i> Waspada (≤ 30 Hari)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                        <i class="bi bi-check-circle-fill"></i> Masa Simpan Aman
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1">
                                    
                                    <!-- 1. Perbarui Tanggal Kedaluwarsa (Modal Cepat) -->
                                    <button type="button"
                                            @click="activeProduct = { id: {{ $p->id }}, name: '{{ addslashes($p->name) }}', code: '{{ $p->code }}', stock: {{ $p->stock }}, unit: '{{ $p->unit }}', expired_date: '{{ $p->expired_date ? $p->expired_date->format('Y-m-d') : '' }}' }; updateModal = true;"
                                            class="rounded bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                            title="Perbarui Tanggal Kedaluwarsa (Batch Baru)">
                                        <i class="bi bi-calendar2-plus text-sm"></i>
                                    </button>

                                    <!-- 2. Edit Seluruh Data Produk -->
                                    <a href="{{ route('admin.products.edit', $p->id) }}"
                                       class="rounded bg-slate-100 p-1.5 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition"
                                       title="Edit Data Produk Lengkap">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <!-- 3. Musnahkan / Kosongkan Stok Kedaluwarsa (Jika ada stok) -->
                                    @if($p->stock > 0)
                                        <form id="dispose-stock-{{ $p->id }}" method="POST" action="{{ route('admin.expiry.dispose', $p->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="confirmAction('dispose-stock-{{ $p->id }}', 'Musnahkan sisa stok sebanyak {{ $p->stock }} {{ $p->unit }} dari {{ addslashes($p->name) }}? Stok akan dikosongkan (0) dan dicatat ke riwayat pengeluaran barang kedaluwarsa.', 'Ya, Musnahkan Stok!')"
                                                    class="rounded bg-amber-50 p-1.5 text-amber-600 hover:bg-amber-600 hover:text-white transition"
                                                    title="Musnahkan / Kosongkan Sisa Stok Kedaluwarsa">
                                                <i class="bi bi-fire text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 4. Hapus Produk Secara Permanen -->
                                    <form id="delete-exp-{{ $p->id }}" method="POST" action="{{ route('admin.expiry.destroy', $p->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                @click="confirmDelete('delete-exp-{{ $p->id }}', 'barang {{ addslashes($p->name) }}')"
                                                class="rounded bg-red-50 p-1.5 text-red-600 hover:bg-red-600 hover:text-white transition"
                                                title="Hapus Barang Secara Permanen">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                <i class="bi bi-calendar2-check text-3xl mb-1 block"></i>
                                Tidak ada data barang yang sesuai dengan filter kedaluwarsa.
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

    <!-- MODAL PERBARUI TANGGAL KEDALUWARSA / BATCH BARU -->
    <div x-show="updateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div @click.outside="updateModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Perbarui Tanggal Kedaluwarsa</h3>
                    <p class="text-[11px] text-slate-500 font-mono" x-text="activeProduct.code + ' - ' + activeProduct.name"></p>
                </div>
                <button @click="updateModal = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>

            <form :action="'{{ url('admin/kedaluwarsa') }}/' + activeProduct.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Tanggal Kedaluwarsa Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expired_date" x-model="activeProduct.expired_date" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-[11px] text-slate-400">Set tanggal baru dari produk atau batch kiriman suplier terbaru.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Tambah Stok Batch Baru (Opsional)
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="add_stock" min="0" value="0" placeholder="0"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span class="text-xs font-semibold text-slate-500" x-text="activeProduct.unit"></span>
                    </div>
                    <span class="text-[11px] text-slate-400">Isi jika sekaligus menambah stok kiriman barang baru. Stok saat ini: <span class="font-bold text-slate-700" x-text="activeProduct.stock + ' ' + activeProduct.unit"></span></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pembaruan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Kiriman batch baru dari suplier sembako"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="updateModal = false" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function confirmAction(formId, message, confirmBtnText = 'Ya, Lanjutkan!') {
        Swal.fire({
            title: 'Konfirmasi Tindakan',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
