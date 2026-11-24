@extends('layouts.app')

@section('title', 'Transaksi Kasir (POS)')
@section('page_title', 'Point of Sale (Terminal Kasir)')
@section('page_subtitle', 'Pindai barcode atau ketik nama barang untuk menambahkan ke keranjang belanja')

@section('content')
<div x-data="posApp()" class="h-full flex flex-col space-y-4">

    <!-- BARIS PENCARIAN BARCODE & PRODUK -->
    <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row gap-3">
            
            <!-- Input Barcode Scanner (AutoFocus & Enter) -->
            <div class="relative flex-1">
                <i class="bi bi-upc-scan absolute left-3 top-2.5 text-slate-400 text-lg"></i>
                <input type="text" x-model="barcodeQuery" @keydown.enter.prevent="scanBarcode()"
                       placeholder="Scan Barcode / Ketik Kode lalu tekan ENTER..."
                       class="w-full rounded-lg border-2 border-blue-400 bg-blue-50/20 py-2.5 pl-10 pr-24 text-sm font-mono focus:border-blue-600 focus:bg-white focus:outline-none shadow-sm"
                       autofocus x-ref="barcodeInput">
                <button type="button" @click="scanBarcode()"
                        class="absolute right-1.5 top-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                    Enter
                </button>
            </div>

            <!-- Pencarian Live Nama Barang -->
            <div class="relative w-full sm:w-80">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts()"
                       placeholder="Cari nama barang..."
                       class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-xs focus:border-blue-500 focus:outline-none">
                
                <!-- Dropdown Hasil Pencarian Nama -->
                <div x-show="searchResults.length > 0" @click.outside="searchResults = []" x-cloak
                     class="absolute left-0 right-0 top-full mt-1 max-h-60 overflow-y-auto rounded-xl bg-white shadow-2xl ring-1 ring-black/10 z-50 divide-y divide-slate-100">
                    <template x-for="item in searchResults" :key="item.id">
                        <button type="button" @click="addToCart(item)"
                                :disabled="item.is_expired || item.stock <= 0"
                                class="w-full text-left p-3 hover:bg-blue-50 transition flex items-center justify-between disabled:opacity-50 disabled:bg-slate-50">
                            <div>
                                <p class="text-xs font-bold text-slate-800" x-text="item.name"></p>
                                <span class="text-[11px] font-mono text-slate-400" x-text="item.code"></span>
                                <template x-if="item.is_expired">
                                    <span class="ml-2 rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-bold text-red-700">KEDALUWARSA</span>
                                </template>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-blue-600" x-text="formatRupiah(item.sell_price)"></p>
                                <span class="text-[10px]" :class="item.stock <= 0 ? 'text-red-500 font-bold' : 'text-slate-400'"
                                      x-text="'Stok: ' + item.stock + ' ' + item.unit"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <!-- AREA UTAMA: DAFTAR KERANJANG (KIRI) & PANEL PEMBAYARAN (KANAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 flex-1 items-start">
        
        <!-- KOLOM 1: TABEL KERANJANG BELANJA (8 KOLOM) -->
        <div class="lg:col-span-7 xl:col-span-8 rounded-xl bg-white shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <i class="bi bi-cart3 text-base text-blue-600"></i>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Keranjang Belanja Pelanggan</h3>
                </div>
                <button type="button" @click="clearCart()" x-show="cart.length > 0"
                        class="text-[11px] font-semibold text-red-600 hover:text-red-800 flex items-center gap-1">
                    <i class="bi bi-trash"></i> Kosongkan
                </button>
            </div>

            <!-- Tabel Daftar Item di Keranjang -->
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Produk</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-center w-36">Jumlah (Qty)</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                            <th class="py-2.5 px-3 text-center w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-2.5 px-3">
                                    <p class="font-bold text-slate-800" x-text="item.name"></p>
                                    <span class="font-mono text-[11px] text-slate-400" x-text="item.code"></span>
                                </td>
                                <td class="py-2.5 px-3 text-right text-slate-600" x-text="formatRupiah(item.sell_price)"></td>
                                <td class="py-2.5 px-3 text-center">
                                    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white">
                                        <button type="button" @click="decreaseQty(index)"
                                                class="px-2 py-1 text-slate-600 hover:bg-slate-100 rounded-l-lg font-bold">
                                            -
                                        </button>
                                        <input type="number" min="1" :max="item.stock" x-model.number="item.quantity" @input="validateQty(index)"
                                               class="w-12 text-center text-xs font-bold text-slate-800 border-x border-slate-200 py-1 focus:outline-none">
                                        <button type="button" @click="increaseQty(index)"
                                                class="px-2 py-1 text-slate-600 hover:bg-slate-100 rounded-r-lg font-bold">
                                            +
                                        </button>
                                    </div>
                                    <span class="block text-[10px] text-slate-400 mt-0.5" x-text="'Maks: ' + item.stock + ' ' + item.unit"></span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-blue-600" x-text="formatRupiah(item.sell_price * item.quantity)"></td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-red-600 p-1">
                                        <i class="bi bi-x-circle text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Tampilan Jika Keranjang Kosong -->
                        <tr x-show="cart.length === 0">
                            <td colspan="5" class="py-16 text-center text-slate-400">
                                <i class="bi bi-cart-x text-4xl mb-2 block text-slate-300"></i>
                                <p class="text-sm font-semibold">Keranjang belanja masih kosong.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Scan barcode produk atau gunakan kotak pencarian nama di atas.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM 2: RINGKASAN PEMBAYARAN & HITUNG KEMBALIAN (4 KOLOM) -->
        <div class="lg:col-span-5 xl:col-span-4 rounded-xl bg-white shadow-sm border border-slate-200 p-5 space-y-5">
            
            <!-- TOTAL BELANJA (Display Besar) -->
            <div class="rounded-xl bg-slate-900 p-4 text-white">
                <span class="text-xs font-semibold uppercase text-slate-400">Total Harus Dibayar</span>
                <h2 class="text-3xl font-extrabold text-emerald-400 mt-1" x-text="formatRupiah(totalAmount)"></h2>
                <div class="mt-2 flex justify-between text-xs text-slate-400 border-t border-slate-800 pt-2">
                    <span>Total Item:</span>
                    <span class="font-bold text-white" x-text="totalItems + ' pcs'"></span>
                </div>
            </div>

            <!-- INPUT UANG TUNAI DITERIMA -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Uang Diterima dari Pelanggan (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" x-model.number="cashAmount" min="0" placeholder="0"
                           class="w-full rounded-lg border-2 border-slate-300 py-2.5 pl-9 pr-3 text-base font-bold text-slate-800 focus:border-blue-600 focus:outline-none">
                </div>

                <!-- Tombol Nominal Cepat -->
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" @click="cashAmount = totalAmount" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        Uang Pas
                    </button>
                    <button type="button" @click="cashAmount = 10000" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        10.000
                    </button>
                    <button type="button" @click="cashAmount = 20000" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        20.000
                    </button>
                    <button type="button" @click="cashAmount = 50000" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        50.000
                    </button>
                    <button type="button" @click="cashAmount = 100000" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        100.000
                    </button>
                    <button type="button" @click="cashAmount = 200000" class="rounded bg-slate-100 hover:bg-slate-200 py-1.5 text-xs font-semibold text-slate-700">
                        200.000
                    </button>
                </div>
            </div>

            <!-- DISPLAY KEMBALIAN -->
            <div class="rounded-xl p-4 border" :class="changeAmount >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-700'">
                        <span x-text="changeAmount >= 0 ? 'Kembalian Pelanggan:' : 'Kurang Bayar:'"></span>
                    </span>
                    <h3 class="text-xl font-bold" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-700'"
                        x-text="formatRupiah(Math.abs(changeAmount))">
                    </h3>
                </div>
            </div>

            <!-- CATATAN TRANSAKSI (OPSIONAL) -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan Struk (Opsional)</label>
                <input type="text" x-model="notes" placeholder="Contoh: Titipan tetangga / Plastik ramah lingkungan"
                       class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <!-- TOMBOL SIMPAN & PROSES TRANSAKSI -->
            <button type="button" @click="processPayment()"
                    :disabled="cart.length === 0 || cashAmount < totalAmount || isProcessing"
                    class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 py-3.5 text-sm font-bold text-white shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span x-show="!isProcessing"><i class="bi bi-check2-circle text-lg"></i> SELESAIKAN PEMBAYARAN</span>
                <span x-show="isProcessing"><i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan Transaksi...</span>
            </button>

        </div>

    </div>

    <!-- MODAL SUKSES TRANSAKSI & CETAK STRUK -->
    <div x-show="successModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <i class="bi bi-check-lg text-3xl"></i>
            </div>
            
            <h3 class="text-lg font-bold text-slate-800">Transaksi Berhasil!</h3>
            <p class="text-xs font-mono text-blue-600 mt-1" x-text="completedTransaction.invoice_number"></p>
            
            <div class="my-4 rounded-xl bg-slate-50 p-4 border border-slate-100 space-y-1.5 text-xs text-left">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Belanja:</span>
                    <span class="font-bold text-slate-800" x-text="formatRupiah(completedTransaction.total_amount)"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Uang Diterima:</span>
                    <span class="font-semibold text-slate-800" x-text="formatRupiah(completedTransaction.cash_amount)"></span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5 text-emerald-600 font-bold text-sm">
                    <span>Kembalian:</span>
                    <span x-text="formatRupiah(completedTransaction.change_amount)"></span>
                </div>
            </div>

            <div class="space-y-2">
                <a :href="completedTransaction.print_url" target="_blank"
                   class="block w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white shadow hover:bg-emerald-700 transition">
                    <i class="bi bi-printer"></i> Cetak Struk Pembelian
                </a>
                <button type="button" @click="resetForm()"
                        class="w-full rounded-xl bg-slate-100 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        barcodeQuery: '',
        searchQuery: '',
        searchResults: [],
        cart: [],
        cashAmount: 0,
        notes: '',
        isProcessing: false,
        successModal: false,
        completedTransaction: {
            invoice_number: '',
            total_amount: 0,
            cash_amount: 0,
            change_amount: 0,
            print_url: '#'
        },

        get totalAmount() {
            return this.cart.reduce((sum, item) => sum + (item.sell_price * item.quantity), 0);
        },

        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get changeAmount() {
            return (this.cashAmount || 0) - this.totalAmount;
        },

        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        },

        // Cari lewat Barcode Scanner
        async scanBarcode() {
            const query = this.barcodeQuery.trim();
            if (!query) return;

            try {
                const res = await fetch(`{{ route('kasir.api.products') }}?q=${encodeURIComponent(query)}`);
                const products = await res.json();

                if (products.length > 0) {
                    const item = products[0]; // Cocokkan barcode
                    this.addToCart(item);
                    this.barcodeQuery = '';
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ditemukan',
                        text: `Barang dengan kode/barcode '${query}' tidak terdaftar di sistem.`,
                        timer: 2500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (err) {
                console.error(err);
            }
        },

        // Live Search Nama Barang
        async searchProducts() {
            const query = this.searchQuery.trim();
            if (query.length < 2) {
                this.searchResults = [];
                return;
            }

            try {
                const res = await fetch(`{{ route('kasir.api.products') }}?q=${encodeURIComponent(query)}`);
                this.searchResults = await res.json();
            } catch (err) {
                console.error(err);
            }
        },

        // Tambah ke Keranjang
        addToCart(item) {
            // Cegah barang kedaluwarsa dijual
            if (item.is_expired) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang Kedaluwarsa!',
                    text: `Produk '${item.name}' sudah melewati tanggal kedaluwarsa (${item.expired_date}) dan DILARANG untuk dijual.`
                });
                return;
            }

            // Cegah jika stok kosong
            if (item.stock <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Habis!',
                    text: `Stok produk '${item.name}' saat ini 0 ${item.unit}.`
                });
                return;
            }

            const existingIndex = this.cart.findIndex(c => c.id === item.id);
            if (existingIndex > -1) {
                if (this.cart[existingIndex].quantity + 1 > item.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Terbatas',
                        text: `Hanya tersisa ${item.stock} ${item.unit} untuk produk '${item.name}'.`,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    return;
                }
                this.cart[existingIndex].quantity++;
            } else {
                this.cart.push({
                    id: item.id,
                    code: item.code,
                    name: item.name,
                    sell_price: item.sell_price,
                    buy_price: item.buy_price,
                    stock: item.stock,
                    unit: item.unit,
                    quantity: 1
                });
            }

            this.searchResults = [];
            this.searchQuery = '';
            this.$refs.barcodeInput.focus();
        },

        increaseQty(index) {
            if (this.cart[index].quantity + 1 > this.cart[index].stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Stok',
                    text: `Maksimal stok tersedia adalah ${this.cart[index].stock} ${this.cart[index].unit}.`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                return;
            }
            this.cart[index].quantity++;
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
            } else {
                this.removeItem(index);
            }
        },

        validateQty(index) {
            const item = this.cart[index];
            if (item.quantity > item.stock) {
                item.quantity = item.stock;
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Stok',
                    text: `Jumlah disesuaikan dengan sisa stok: ${item.stock} ${item.unit}.`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
            if (item.quantity < 1 || isNaN(item.quantity)) {
                item.quantity = 1;
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            this.cart = [];
            this.cashAmount = 0;
            this.notes = '';
        },

        // Proses Pembayaran dan Simpan Transaksi (AJAX)
        async processPayment() {
            if (this.cart.length === 0) return;

            if (this.cashAmount < this.totalAmount) {
                Swal.fire({
                    icon: 'error',
                    title: 'Uang Kurang',
                    text: 'Uang tunai yang diterima kurang dari total belanja pelanggan!'
                });
                return;
            }

            this.isProcessing = true;

            const payload = {
                items: this.cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity
                })),
                cash_amount: this.cashAmount,
                notes: this.notes
            };

            try {
                const response = await fetch("{{ route('kasir.api.transaction.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    this.completedTransaction = {
                        invoice_number: data.invoice_number,
                        total_amount: this.totalAmount,
                        cash_amount: this.cashAmount,
                        change_amount: this.changeAmount,
                        print_url: data.print_url
                    };
                    this.successModal = true;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Transaksi',
                        text: data.message || 'Terjadi kesalahan sistem.'
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat menghubungi server.'
                });
            } finally {
                this.isProcessing = false;
            }
        },

        resetForm() {
            this.clearCart();
            this.successModal = false;
            this.$nextTick(() => {
                this.$refs.barcodeInput.focus();
            });
        }
    }
}
</script>
@endpush

