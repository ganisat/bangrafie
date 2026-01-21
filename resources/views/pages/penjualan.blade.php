@extends('layouts.app')

@section('title','Penjualan')
@section('header','Penjualan')
@section('subheader','Tren harian/mingguan & perbandingan periode (WoW/MoM)')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');

    $fmtDelta = function($d) {
        $sign = $d['up'] ? '+' : '';
        return $sign . number_format($d['pct'], 1, ',', '.') . '%';
    };

    $badge = function($d) use ($fmtDelta) {
        $cls = $d['up'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
        return '<span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs '.$cls.'">'.$fmtDelta($d).'</span>';
    };
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('penjualan') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="text-xs text-slate-600">Mode</label>
                <select name="mode" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="harian"  {{ $mode === 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan"{{ $mode === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                </select>
            </div>

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

            <div class="md:col-span-3 flex gap-2">
                <button class="flex-1 rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('penjualan') }}"
                   class="flex-1 rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 text-xs text-slate-500">
            Mode <span class="font-semibold text-slate-700">{{ $mode }}</span> menampilkan agregasi omzet, transaksi, dan AOV.
        </div>
    </div>

    {{-- Perbandingan --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- WoW --}}
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold">Perbandingan WoW</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $woW_prev_start->format('d M') }}–{{ $woW_prev_end->format('d M Y') }}
                        vs
                        {{ $woW_this_start->format('d M') }}–{{ $woW_this_end->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Metrik</th>
                            <th class="py-2 text-right font-medium">Minggu Lalu</th>
                            <th class="py-2 text-right font-medium">Minggu Ini</th>
                            <th class="py-2 text-right font-medium">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2">Omzet</td>
                            <td class="py-2 text-right">{{ $rupiah($wow['prev']['omzet']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($wow['this']['omzet']) }}</td>
                            <td class="py-2 text-right">{!! $badge($wow['delta']['omzet']) !!}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2">Transaksi</td>
                            <td class="py-2 text-right">{{ number_format($wow['prev']['trx']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($wow['this']['trx']) }}</td>
                            <td class="py-2 text-right">{!! $badge($wow['delta']['trx']) !!}</td>
                        </tr>
                        <tr>
                            <td class="py-2">AOV</td>
                            <td class="py-2 text-right">{{ $rupiah($wow['prev']['aov']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($wow['this']['aov']) }}</td>
                            <td class="py-2 text-right">{!! $badge($wow['delta']['aov']) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MoM --}}
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold">Perbandingan MoM</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $mtd_prev_start->format('d M') }}–{{ $mtd_prev_end->format('d M Y') }}
                        vs
                        {{ $mtd_this_start->format('d M') }}–{{ $mtd_this_end->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Metrik</th>
                            <th class="py-2 text-right font-medium">Bulan Lalu</th>
                            <th class="py-2 text-right font-medium">Bulan Ini</th>
                            <th class="py-2 text-right font-medium">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2">Omzet</td>
                            <td class="py-2 text-right">{{ $rupiah($mom['prev']['omzet']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($mom['this']['omzet']) }}</td>
                            <td class="py-2 text-right">{!! $badge($mom['delta']['omzet']) !!}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2">Transaksi</td>
                            <td class="py-2 text-right">{{ number_format($mom['prev']['trx']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($mom['this']['trx']) }}</td>
                            <td class="py-2 text-right">{!! $badge($mom['delta']['trx']) !!}</td>
                        </tr>
                        <tr>
                            <td class="py-2">AOV</td>
                            <td class="py-2 text-right">{{ $rupiah($mom['prev']['aov']) }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($mom['this']['aov']) }}</td>
                            <td class="py-2 text-right">{!! $badge($mom['delta']['aov']) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Tren --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold">Tren {{ $mode === 'mingguan' ? 'Mingguan' : 'Harian' }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan rentang filter di atas</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-left font-medium">{{ $mode === 'mingguan' ? 'Minggu' : 'Tanggal' }}</th>
                        <th class="py-2 text-left font-medium">Range</th>
                        <th class="py-2 text-right font-medium">Omzet</th>
                        <th class="py-2 text-right font-medium">Transaksi</th>
                        <th class="py-2 text-right font-medium">AOV</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trendRows as $r)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 font-medium">{{ $r['label'] }}</td>
                            <td class="py-2 text-slate-600">{{ $r['range'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                            <td class="py-2 text-right">{{ number_format($r['trx']) }}</td>
                            <td class="py-2 text-right">{{ $rupiah($r['aov']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-xs text-slate-500">
            Next step: data ini diisi dari tabel transaksi (SUM total, COUNT id, AOV = omzet/transaksi).
        </div>
    </div>

</div>
@endsection
