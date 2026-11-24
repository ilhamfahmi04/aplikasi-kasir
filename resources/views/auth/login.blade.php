@extends('layouts.guest')

@section('title', 'Masuk ke Sistem Kasir')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10" x-data="{
    fillAdmin() {
        document.getElementById('email').value = 'admin@kelontong.com';
        document.getElementById('password').value = 'admin123';
    },
    fillKasir() {
        document.getElementById('email').value = 'kasir@kelontong.com';
        document.getElementById('password').value = 'kasir123';
    }
}">
    <div class="w-full max-w-md">

        <!-- HEADER LOGO & JUDUL -->
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-2xl text-white shadow-lg">
                <i class="bi bi-shop"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sistem Kasir Toko Kelontong</h1>
            <p class="mt-1 text-xs text-slate-500">Masuk untuk mengelola penjualan, inventaris, dan transaksi kasir</p>
        </div>

        <!-- KARTU FORM LOGIN -->
        <div class="rounded-2xl bg-white p-7 shadow-xl border border-slate-200">

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" novalidate>
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold text-slate-700">Email Pengguna</label>
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@kelontong.com') }}"
                           autocomplete="username" required autofocus
                           placeholder="nama@kelontong.com"
                           class="w-full rounded-lg border px-3.5 py-2.5 text-xs outline-none transition focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('email') ? 'border-red-400' : 'border-slate-300' }}">
                    @error('email')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                        <button type="button" @click="show = !show" class="text-[11px] text-blue-600 hover:underline"
                                x-text="show ? 'Sembunyikan' : 'Lihat'"></button>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" value="admin123"
                               autocomplete="current-password" required
                               placeholder="••••••••"
                               class="w-full rounded-lg border px-3.5 py-2.5 text-xs outline-none transition focus:ring-2 focus:ring-blue-500
                                      {{ $errors->has('password') ? 'border-red-400' : 'border-slate-300' }}">
                    </div>
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ingat Saya -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        class="w-full rounded-xl bg-blue-600 py-3 text-xs font-bold text-white shadow-md transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- KOTAK AKUN CONTOH / QUICK FILL UNTUK PENGUJIAN -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <span class="block text-[11px] font-bold text-slate-500 mb-2 uppercase text-center">Akun Uji Coba:</span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="fillAdmin()"
                            class="rounded-lg border border-blue-200 bg-blue-50/60 p-2 text-left hover:bg-blue-100 transition">
                        <p class="text-[11px] font-bold text-blue-900">Admin Toko</p>
                        <p class="text-[10px] text-blue-600 truncate">admin@kelontong.com</p>
                        <span class="text-[9px] text-slate-400">Pass: admin123</span>
                    </button>
                    <button type="button" @click="fillKasir()"
                            class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-2 text-left hover:bg-emerald-100 transition">
                        <p class="text-[11px] font-bold text-emerald-900">Petugas Kasir</p>
                        <p class="text-[10px] text-emerald-600 truncate">kasir@kelontong.com</p>
                        <span class="text-[9px] text-slate-400">Pass: kasir123</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-5 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Dirancang untuk Pemilik Toko & Kasir.
        </p>

    </div>
</div>
@endsection
