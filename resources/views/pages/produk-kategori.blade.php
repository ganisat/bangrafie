@extends('layouts.app')

@section('title','Produk & Kategori')
@section('header','Produk & Kategori')
@section('subheader','Top produk, top kategori, dan slow moving')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('produk_kategori') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Tanggal Mulai</label>
                <input type="date" name="start" value="{{ $start }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Tanggal Selesai</label>
                <input type="date" name="end" value="{{ $end }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Urutkan berdasarkan</label>
                <select name="by" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="qty" {{ $by==='qty' ? 'selected' : '' }}>Qty</option>
                    <option value="omzet" {{ $by==='omzet' ? 'selected' : '' }}>Omzet</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Channel (opsional)</label>
                <select name="channel" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="all" {{ $channel==='all' ? 'selected' : '' }}>Semua</option>
                    <option value="offline" {{ $channel==='offline' ? 'selected' : '' }}>Offline</option>
                    <option value="online" {{ $channel==='online' ? 'selected' : '' }}>Online</option>
                    <option value="wa" {{ $channel==='wa' ? 'selected' : '' }}>WA</option>
                    <option value="marketplace" {{ $channel==='marketplace' ? 'selected' : '' }}>Marketplace</option>
                </select>
            </div>

            <div class="md:col-span-12 flex gap-2">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('produk_kategori') }}"
                   class="rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Periode</p>
                <p class="font-semibold mt-1">{{ $summary['periode'] }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Qty (Top Produk)</p>
                <p class="font-semibold mt-1">{{ number_format($summary['total_produk_terjual']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Slow Moving</p>
                <p class="font-semibold mt-1">{{ number_format($summary['slow_moving_count']) }} item</p>
            </div>
        </div>
    </div>

    {{-- Top Produk --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold">Top Produk</p>
                <p class="text-xs text-slate-500 mt-0.5">Diurutkan berdasarkan {{ $by }}</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">SKU</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Qty</th>
                        <th class="py-2 text-right font-medium">Omzet</th>
                        <th class="py-2 text-right font-medium">AOV</th>
                        <th class="py-2 text-right font-medium">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProduk as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                            <td class="py-2">{{ $r['nama'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right">{{ number_format($r['qty']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['aov']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['margin_pct'], 0) }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Kategori --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div>
            <p class="text-sm font-semibold">Top Kategori</p>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan kontribusi kategori</p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Qty</th>
                        <th class="py-2 text-right font-medium">Omzet</th>
                        <th class="py-2 text-right font-medium">Produk Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topKategori as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right">{{ number_format($r['qty']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['produk_aktif']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Slow Moving --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div>
            <p class="text-sm font-semibold">Slow Moving</p>
            <p class="text-xs text-slate-500 mt-0.5">Stok masih ada, penjualan periode rendah</p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">SKU</th>
                        <th class="py-2 text-left font-medium">Nama</th>
                        <th class="py-2 text-left font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Stok</th>
                        <th class="py-2 text-right font-medium">Qty Periode</th>
                        <th class="py-2 text-right font-medium">Omzet Periode</th>
                        <th class="py-2 text-right font-medium">Terakhir Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slowMoving as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                            <td class="py-2">{{ $r['nama'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($r['stok']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['qty_periode']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['omzet_periode']) }}</td>
                            <td class="py-2 text-right">{{ $r['last_sold'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-xs text-slate-500">
            Next step: slow moving bisa dibuat rule: <span class="font-semibold">qty_periode = 0</span> atau <span class="font-semibold">qty_periode &lt; X</span> dan <span class="font-semibold">stok &gt; 0</span>.
        </div>
    </div>

</div>
@endsection
