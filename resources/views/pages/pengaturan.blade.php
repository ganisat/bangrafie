@extends('layouts.app')

@section('title','Pengaturan')
@section('header','Pengaturan')
@section('subheader','Target, margin/HPP, batas stok minimum, dan user toko')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

@section('content')
<div class="space-y-6">

    @if(session('ok'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <p class="font-semibold mb-1">Ada error:</p>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Settings --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-sm font-semibold">Pengaturan Utama</p>
        <p class="text-xs text-slate-500 mt-0.5">Atur target dan aturan default toko</p>

        <form method="POST" action="{{ route('pengaturan.save') }}" class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-4">
            @csrf

            {{-- Target --}}
            <div class="md:col-span-12">
                <p class="text-sm font-semibold">Target</p>
                <p class="text-xs text-slate-500 mt-0.5">Dipakai untuk indikator capaian di dashboard</p>
            </div>

            <div class="md:col-span-6">
                <label class="text-xs text-slate-600">Target Omzet Harian</label>
                <input type="number" min="0" name="target_harian" value="{{ old('target_harian', $settings['target_harian']) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="contoh: 2000000">
            </div>
            <div class="md:col-span-6">
                <label class="text-xs text-slate-600">Target Omzet Bulanan</label>
                <input type="number" min="0" name="target_bulanan" value="{{ old('target_bulanan', $settings['target_bulanan']) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="contoh: 60000000">
            </div>

            {{-- Margin/HPP --}}
            <div class="md:col-span-12 mt-2">
                <p class="text-sm font-semibold">Margin / HPP</p>
                <p class="text-xs text-slate-500 mt-0.5">Default margin dipakai untuk estimasi profit kalau HPP belum lengkap</p>
            </div>

            <div class="md:col-span-6">
                <label class="text-xs text-slate-600">Default Margin (%)</label>
                <input type="number" min="0" max="100" name="default_margin_pct"
                       value="{{ old('default_margin_pct', $settings['default_margin_pct']) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-6">
                <label class="text-xs text-slate-600">Mode HPP</label>
                <select name="default_hpp_mode" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="manual" {{ old('default_hpp_mode', $settings['default_hpp_mode'])==='manual' ? 'selected' : '' }}>
                        Manual (global/estimasi)
                    </option>
                    <option value="per_produk" {{ old('default_hpp_mode', $settings['default_hpp_mode'])==='per_produk' ? 'selected' : '' }}>
                        Per Produk (placeholder)
                    </option>
                </select>
                <p class="text-xs text-slate-500 mt-1">Nanti kalau sudah ada kolom HPP di produk, mode ini bisa dipakai.</p>
            </div>

            {{-- Batas stok minimum --}}
            <div class="md:col-span-12 mt-2">
                <p class="text-sm font-semibold">Batas Stok Minimum</p>
                <p class="text-xs text-slate-500 mt-0.5">Default minimum stok global (jika produk belum punya min stok sendiri)</p>
            </div>

            <div class="md:col-span-6">
                <label class="text-xs text-slate-600">Default Min Stok</label>
                <input type="number" min="0" name="default_min_stok"
                       value="{{ old('default_min_stok', $settings['default_min_stok']) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-6 flex items-end">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 w-full">
                    <p class="text-xs text-slate-500">Contoh penggunaan</p>
                    <p class="text-sm font-semibold mt-1">Stok kritis jika stok ≤ min stok</p>
                    <p class="text-xs text-slate-500 mt-1">Min stok produk → pakai per item, jika kosong → pakai default.</p>
                </div>
            </div>

            <div class="md:col-span-12 flex gap-2 mt-2">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    {{-- User Toko --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold">User Toko</p>
                <p class="text-xs text-slate-500 mt-0.5">Kelola user yang bisa akses dashboard</p>
            </div>
        </div>

        {{-- Form tambah user --}}
        <form method="POST" action="{{ route('pengaturan.users.create') }}" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            @csrf
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Nama</label>
                <input name="name" value="{{ old('name') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="Nama user">
            </div>
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Email</label>
                <input name="email" type="email" value="{{ old('email') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="email@toko.com">
            </div>
            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Password</label>
                <input name="password" type="password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="min 6 char">
            </div>
            <div class="md:col-span-1">
                <button class="w-full rounded-xl bg-emerald-600 text-white px-3 py-2 text-sm font-semibold">
                    +
                </button>
            </div>
        </form>

        {{-- List user --}}
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">ID</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Email</th>
                        <th class="py-2 text-right font-medium">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $u->id }}</td>
                            <td class="py-2">{{ $u->name }}</td>
                            <td class="py-2 text-slate-600">{{ $u->email }}</td>
                            <td class="py-2 text-right text-slate-600">{{ $u->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-500">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-xs text-slate-500">
            Next step: tambah role (owner/kasir/admin) + fitur reset password.
        </div>
    </div>

</div>
@endsection
