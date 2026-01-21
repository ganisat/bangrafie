@extends('layouts.app')

@section('title','Pasar')
@section('header','Pasar')
@section('subheader','Offline vs Online/WA/Marketplace')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('pasar') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Tanggal Mulai</label>
                <input type="date" name="start" value="{{ $summary['start'] }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Tanggal Selesai</label>
                <input type="date" name="end" value="{{ $summary['end'] }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button class="flex-1 rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Terapkan</button>
                <a href="{{ route('pasar') }}"
                   class="flex-1 rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Omzet</p>
                <p class="font-semibold mt-1">{{ $rupiah($summary['total_omzet']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Transaksi</p>
                <p class="font-semibold mt-1">{{ number_format($summary['total_trx']) }}</p>
            </div>
        </div>
    </div>

    {{-- Ringkasan per pasar --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-sm font-semibold">Ringkasan Penjualan per Pasar</p>
        <p class="text-xs text-slate-500 mt-0.5">Porsi omzet & transaksi per segmen</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">Pasar</th>
                        <th class="py-2 text-right font-medium">Transaksi</th>
                        <th class="py-2 text-right font-medium">Porsi Trx</th>
                        <th class="py-2 text-right font-medium">Omzet</th>
                        <th class="py-2 text-right font-medium">Porsi Omzet</th>
                        <th class="py-2 text-right font-medium">AOV</th>
                        <th class="py-2 text-right font-medium">Profit Est.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['pasar'] }}</td>
                            <td class="py-2 text-right">{{ number_format($r['trx']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['porsi_trx_pct'], 0) }}%</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['porsi_omzet_pct'], 0) }}%</td>
                            <td class="py-2 text-right">{{ $rupiah($r['aov']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['profit_est']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top produk per pasar --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-sm font-semibold">Top Produk per Pasar</p>
        <p class="text-xs text-slate-500 mt-0.5">Produk unggulan di tiap segmen</p>

        <div class="mt-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
            @foreach($topProdukPerPasar as $pasar => $list)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="font-semibold text-sm">{{ $pasar }}</p>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-slate-500">
                                <tr class="border-b border-slate-200">
                                    <th class="py-2 text-left font-medium">SKU</th>
                                    <th class="py-2 text-left font-medium">Nama</th>
                                    <th class="py-2 text-right font-medium">Qty</th>
                                    <th class="py-2 text-right font-medium">Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $r)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 font-medium">{{ $r['sku'] }}</td>
                                        <td class="py-2">{{ $r['nama'] }}</td>
                                        <td class="py-2 text-right">{{ number_format($r['qty']) }}</td>
                                        <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-xs text-slate-500">
            Next step: pasar diisi dari kolom <span class="font-semibold">channel/pasar</span> pada transaksi (offline/online/wa/marketplace).
        </div>
    </div>

</div>
@endsection
