@extends('layouts.app')

@section('title','Stok & Reorder')
@section('header','Stok & Reorder')
@section('subheader','Stok saat ini, stok kritis, dan saran reorder')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');

    $statusPill = function($status) {
        if ($status === 'kritis') {
            return '<span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700">Kritis</span>';
        }
        return '<span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Normal</span>';
    };
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('stok_reorder') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Cari (SKU/Nama/Kategori)</label>
                <input name="q" value="{{ $q }}" placeholder="contoh: kopi / SKU-001"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Filter</label>
                <select name="filter" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="all" {{ $filter==='all' ? 'selected' : '' }}>Semua</option>
                    <option value="kritis" {{ $filter==='kritis' ? 'selected' : '' }}>Kritis</option>
                    <option value="normal" {{ $filter==='normal' ? 'selected' : '' }}>Normal</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Sort</label>
                <select name="sort" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="stok_asc" {{ $sort==='stok_asc' ? 'selected' : '' }}>Stok (asc)</option>
                    <option value="stok_desc" {{ $sort==='stok_desc' ? 'selected' : '' }}>Stok (desc)</option>
                    <option value="nama_asc" {{ $sort==='nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Min stok global (opsional)</label>
                <input type="number" min="0" name="min_rule" value="{{ $minRule }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                       placeholder="0 = per item">
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Target cover (hari)</label>
                <input type="number" min="1" name="cover_day" value="{{ $coverDay }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-12 flex gap-2">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('stok_reorder') }}"
                   class="rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Item</p>
                <p class="font-semibold mt-1">{{ number_format($summary['total_item']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Stok Kritis</p>
                <p class="font-semibold mt-1">{{ number_format($summary['kritis']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Butuh Reorder</p>
                <p class="font-semibold mt-1">{{ number_format($summary['butuh_reorder']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Estimasi Modal Reorder</p>
                <p class="font-semibold mt-1">{{ $rupiah($summary['estimasi_modal_reorder']) }}</p>
            </div>
        </div>
    </div>

    {{-- Saran Reorder --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div>
            <p class="text-sm font-semibold">Saran Reorder</p>
            <p class="text-xs text-slate-500 mt-0.5">
                Rekomendasi qty = (target cover {{ $summary['cover_day'] }} hari + buffer lead time) - stok
            </p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">SKU</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Stok</th>
                        <th class="py-2 text-right font-medium">Min</th>
                        <th class="py-2 text-right font-medium">Avg/Hari</th>
                        <th class="py-2 text-right font-medium">Lead Time</th>
                        <th class="py-2 text-right font-medium">Saran Qty</th>
                        <th class="py-2 text-right font-medium">Est. Modal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saranReorder as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                            <td class="py-2">{{ $r['nama'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($r['stok']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['min_eff']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['avg_daily_sold'], 1, ',', '.') }}</td>
                            <td class="py-2 text-right">{{ number_format($r['lead_time_hari']) }}h</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($r['reorder_qty']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['reorder_cost']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-6 text-center text-slate-500">Tidak ada saran reorder</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stok Kritis --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div>
            <p class="text-sm font-semibold">Stok Kritis</p>
            <p class="text-xs text-slate-500 mt-0.5">Item dengan stok ≤ minimum</p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">SKU</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Stok</th>
                        <th class="py-2 text-right font-medium">Min</th>
                        <th class="py-2 text-right font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokKritis as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                            <td class="py-2">{{ $r['nama'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($r['stok']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['min_eff']) }}</td>
                            <td class="py-2 text-right">{!! $statusPill($r['status']) !!}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">Tidak ada stok kritis</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stok Saat Ini --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div>
            <p class="text-sm font-semibold">Stok Saat Ini</p>
            <p class="text-xs text-slate-500 mt-0.5">Daftar inventory (semua item)</p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">SKU</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Stok</th>
                        <th class="py-2 text-right font-medium">Min</th>
                        <th class="py-2 text-right font-medium">HPP</th>
                        <th class="py-2 text-right font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                            <td class="py-2">{{ $r['nama'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($r['stok']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['min_eff']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['hpp']) }}</td>
                            <td class="py-2 text-right">{!! $statusPill($r['status']) !!}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
