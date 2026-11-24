<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – {{ config('app.name', 'Sistem Kasir Toko Kelontong') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR (Biru Tua Modern) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 text-slate-200 transition-transform duration-300 ease-in-out md:static md:translate-x-0 shadow-xl">
            
            <!-- Sidebar Header -->
            <div class="flex h-16 items-center justify-between border-b border-slate-800 px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md">
                        <i class="bi bi-shop text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold tracking-tight text-white leading-tight">KASIR KELONTONG</h1>
                        <span class="text-[11px] font-medium text-blue-400">Toko Kelontong Modern</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white md:hidden">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                @if(auth()->user()->isAdmin())
                    <!-- MENU ADMIN -->
                    <div class="px-3 pb-2 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Utama</div>
                    
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-speedometer2 text-base"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="px-3 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Katalog & Stok</div>

                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-tags text-base"></i>
                        <span>Kategori Barang</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-box-seam text-base"></i>
                        <span>Data Barang</span>
                    </a>

                    <a href="{{ route('admin.stock.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.stock.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-arrow-left-right text-base"></i>
                        <span>Riwayat & Tambah Stok</span>
                    </a>

                    <a href="{{ route('admin.expiry.index') }}"
                       class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.expiry.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-calendar2-x text-base"></i>
                            <span>Monitoring Kedaluwarsa</span>
                        </div>
                        @php
                            $expiredCount = \App\Models\Product::whereNotNull('expired_date')->where('expired_date', '<', now()->toDateString())->count();
                        @endphp
                        @if($expiredCount > 0)
                            <span class="rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $expiredCount }}</span>
                        @endif
                    </a>

                    <div class="px-3 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Transaksi & Kasir</div>

                    <a href="{{ route('kasir.transaction') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-emerald-400 hover:bg-slate-800 hover:text-emerald-300">
                        <i class="bi bi-cash-coin text-base"></i>
                        <span>Buka Menu Kasir (POS)</span>
                    </a>

                    <a href="{{ route('admin.transactions.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-receipt text-base"></i>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <div class="px-3 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Laporan Keuangan</div>

                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-graph-up-arrow text-base"></i>
                        <span>Laporan Rekap Penjualan</span>
                    </a>

                    <div class="px-3 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pengaturan</div>

                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-people text-base"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                @else
                    <!-- MENU KASIR -->
                    <div class="px-3 pb-2 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menu Kasir</div>
                    
                    <a href="{{ route('kasir.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('kasir.dashboard') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-speedometer2 text-base"></i>
                        <span>Dashboard Kasir</span>
                    </a>

                    <a href="{{ route('kasir.transaction') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('kasir.transaction') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-cart3 text-base"></i>
                        <span>Transaksi Penjualan (POS)</span>
                    </a>

                    <a href="{{ route('kasir.history') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('kasir.history') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-clock-history text-base"></i>
                        <span>Riwayat Transaksi Saya</span>
                    </a>
                @endif

            </div>

            <!-- Sidebar User Profile -->
            <div class="border-t border-slate-800 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white uppercase">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                            <span class="inline-block rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-blue-400">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="text-slate-400 hover:text-red-400 transition p-1.5 rounded-lg hover:bg-slate-800">
                            <i class="bi bi-box-arrow-right text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex flex-1 flex-col overflow-hidden">

            <!-- NAVBAR ATAS -->
            <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6 shadow-sm">
                
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                    <div>
                        <h2 class="text-base md:text-lg font-bold text-slate-800 leading-tight">@yield('page_title', 'Dashboard')</h2>
                        <p class="text-xs text-slate-500 hidden sm:block">@yield('page_subtitle', 'Selamat datang di Aplikasi Kasir Toko Kelontong')</p>
                    </div>
                </div>

                <!-- Right Header Actions (Notifikasi & Profil) -->
                <div class="flex items-center gap-3 md:gap-4">
                    
                    @php
                        // Alert Kedaluwarsa & Stok Menipis
                        $today = now()->toDateString();
                        $in30Days = now()->addDays(30)->toDateString();

                        $expiredItems = \App\Models\Product::whereNotNull('expired_date')->where('expired_date', '<', $today)->count();
                        $expiringSoon = \App\Models\Product::whereNotNull('expired_date')->whereBetween('expired_date', [$today, $in30Days])->count();
                        $lowStockItems = \App\Models\Product::whereColumn('stock', '<=', 'min_stock')->count();
                        $totalAlerts = $expiredItems + $expiringSoon + $lowStockItems;
                    @endphp

                    <!-- Notification Bell Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-blue-600 transition">
                            <i class="bi bi-bell text-xl"></i>
                            @if($totalAlerts > 0)
                                <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white animate-pulse">
                                    {{ $totalAlerts }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 rounded-xl bg-white p-3 shadow-2xl ring-1 ring-black/5 z-50">
                            
                            <div class="flex items-center justify-between border-b pb-2 mb-2">
                                <span class="font-bold text-sm text-slate-800">Pemberitahuan Sistem</span>
                                <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full">{{ $totalAlerts }} Baru</span>
                            </div>

                            <div class="space-y-2 max-h-72 overflow-y-auto text-xs">
                                @if($expiredItems > 0)
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.expiry.index') : '#' }}" class="flex items-start gap-2.5 p-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
                                        <i class="bi bi-exclamation-octagon-fill text-red-600 text-base"></i>
                                        <div>
                                            <p class="font-bold">{{ $expiredItems }} Barang Telah Kedaluwarsa!</p>
                                            <p class="text-slate-500">Barang ini tidak boleh dijual dan harus dipisahkan.</p>
                                        </div>
                                    </a>
                                @endif

                                @if($expiringSoon > 0)
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.expiry.index') : '#' }}" class="flex items-start gap-2.5 p-2 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 transition">
                                        <i class="bi bi-clock-history text-amber-600 text-base"></i>
                                        <div>
                                            <p class="font-bold">{{ $expiringSoon }} Barang Mendekati Kedaluwarsa</p>
                                            <p class="text-slate-500">Kedaluwarsa dalam kurun waktu 30 hari ke depan.</p>
                                        </div>
                                    </a>
                                @endif

                                @if($lowStockItems > 0)
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.products.index', ['stock_filter' => 'low']) : '#' }}" class="flex items-start gap-2.5 p-2 rounded-lg bg-yellow-50 text-yellow-800 hover:bg-yellow-100 transition">
                                        <i class="bi bi-box-seam text-yellow-600 text-base"></i>
                                        <div>
                                            <p class="font-bold">{{ $lowStockItems }} Barang Stok Menipis</p>
                                            <p class="text-slate-500">Stok berada pada atau di bawah batas minimum.</p>
                                        </div>
                                    </a>
                                @endif

                                @if($totalAlerts == 0)
                                    <div class="py-6 text-center text-slate-400">
                                        <i class="bi bi-check-circle text-2xl text-emerald-500"></i>
                                        <p class="mt-1">Semua stok & tanggal aman!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Jam & Tanggal Sekarang -->
                    <div class="hidden lg:block text-right border-l pl-4 border-slate-200">
                        <div class="text-xs font-semibold text-slate-700" id="liveClock"></div>
                        <div class="text-[11px] text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</div>
                    </div>

                    <!-- User Menu / Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>

                </div>

            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            const el = document.getElementById('liveClock');
            if (el) el.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <!-- SweetAlert2 Flash Notifications -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: "{{ session('error') }}",
                timer: 4000,
                showConfirmButton: true
            });
        @endif

        function confirmDelete(formId, itemName = 'data ini') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data ${itemName} akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
