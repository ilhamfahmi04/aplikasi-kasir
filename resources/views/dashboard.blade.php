{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="rounded-2xl bg-white p-8 shadow ring-1 ring-slate-200">
    <h2 class="text-xl font-bold">Halo, {{ auth()->user()->name }} 👋</h2>
    <p class="mt-2 text-slate-600">
        Login berhasil. Anda masuk sebagai
        <span class="font-semibold uppercase">{{ auth()->user()->role }}</span>.
    </p>
</div>
@endsection