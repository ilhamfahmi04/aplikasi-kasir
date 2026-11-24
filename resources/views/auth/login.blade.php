@extends('layouts.guest')

@section('title', 'Masuk ke Sistem Kasir')

@section('content')
<div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 px-4 py-12 overflow-hidden selection:bg-blue-600 selection:text-white">

    <!-- DEKORASI LATAR BELAKANG AMBIENT GLOW -->
    <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">

        <!-- BRAND LOGO & HEADER -->
        <div class="text-center mb-8">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow-xl shadow-blue-500/25 ring-4 ring-white/10 mb-4 transition-transform hover:scale-105 duration-300">
                <i class="bi bi-shop text-3xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                Sistem Kasir Kelontong
            </h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-400">
                Silakan masuk dengan akun terdaftar Anda untuk memulai
            </p>
        </div>

        <!-- KARTU FORM LOGIN -->
        <div class="rounded-3xl bg-white p-7 sm:p-9 shadow-2xl shadow-slate-950/50 border border-slate-100">

            {{-- Pesan Status / Flash Message --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-2.5 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-semibold text-emerald-800">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 flex items-center gap-2.5 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-xs font-semibold text-blue-800">
                    <i class="bi bi-info-circle-fill text-blue-600 text-sm"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5" novalidate>
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-envelope text-base"></i>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                               autocomplete="username" required autofocus
                               placeholder="nama@kelontong.com"
                               class="w-full rounded-xl border bg-slate-50/50 py-3 pl-10 pr-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition-all focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 {{ $errors->has('email') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 hover:border-slate-300' }}">
                    </div>
                    @error('email')
                        <p class="mt-1.5 flex items-center gap-1 text-[11px] font-semibold text-red-600">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Input Password -->
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kata Sandi (Password)
                        </label>
                        <button type="button" @click="show = !show"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition flex items-center gap-1">
                            <i :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            <span x-text="show ? 'Sembunyikan' : 'Tampilkan'"></span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-shield-lock text-base"></i>
                        </div>
                        <input id="password" name="password" :type="show ? 'text' : 'password'"
                               autocomplete="current-password" required
                               placeholder="••••••••••••"
                               class="w-full rounded-xl border bg-slate-50/50 py-3 pl-10 pr-11 text-xs font-medium text-slate-800 placeholder-slate-400 transition-all focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 {{ $errors->has('password') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 hover:border-slate-300' }}">
                    </div>
                    @error('password')
                        <p class="mt-1.5 flex items-center gap-1 text-[11px] font-semibold text-red-600">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Opsi Ingat Saya -->
                <div class="flex items-center justify-between pt-1">
                    <label class="group flex items-center gap-2.5 text-xs text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               class="h-4 w-4 rounded-md border-slate-300 text-blue-600 transition focus:ring-blue-500 focus:ring-offset-0">
                        <span class="group-hover:text-slate-800">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Tombol Submit Masuk -->
                <button type="submit"
                        class="w-full relative flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3.5 px-4 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-600/30 transition-all hover:from-blue-700 hover:to-indigo-700 hover:shadow-blue-600/40 focus:outline-none focus:ring-4 focus:ring-blue-500/25 active:scale-[0.99]">
                    <span>Masuk ke Akun</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </form>

            <!-- FOOTER KEAMANAN SISTEM -->
            <div class="mt-7 pt-5 border-t border-slate-100 flex items-center justify-center gap-2 text-[11px] text-slate-400 font-medium">
                <i class="bi bi-shield-check text-emerald-600 text-sm"></i>
                <span>Koneksi Aman & Terlindungi Enkripsi SSL</span>
            </div>

        </div>

        <!-- COPYRIGHT FOOTER -->
        <p class="mt-8 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Sistem Kasir Toko Kelontong. Hak Cipta Dilindungi.
        </p>

    </div>
</div>
@endsection
