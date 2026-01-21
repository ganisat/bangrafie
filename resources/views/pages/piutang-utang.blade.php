@extends('layouts.app')

@section('title','Piutang/Utang')
@section('header','Piutang/Utang')
@section('subheader','Daftar jatuh tempo, total piutang & utang')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');

    $statusPill = function($s) {
        return $s === 'paid'
            ? '<span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Lunas</span>'
            : '<span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Open</span>';
    };

    $sisa = fn($r) => max(0, (int)$r['nominal'] - (int)$r['dibayar']);
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('piutang_utang') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Jatuh Tempo Mulai</label>
                <input type="date" name="start" value="{{ $summary['start'] }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Jatuh Tempo Selesai</label>
                <input type="date" name="end" value="{{ $summary['end'] }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Status</label>
                <select name="status" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="open" {{ $summary['status']==='open' ? 'selected' : '' }}>Open</option>
                    <option value="paid" {{ $summary['status']==='paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="all"  {{ $summary['status']==='all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-slate-600">Tipe</label>
                <select name="type" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="all" {{ $summary['type']==='all' ? 'selected' : '' }}>Piutang & Utang</option>
                    <option value="piutang" {{ $summary['type']==='piutang' ? 'selected' : '' }}>Piutang saja</option>
                    <option value="utang" {{ $summary['type']==='utang' ? 'selected' : '' }}>Utang saja</option>
                </select>
            </div>

            <div class="md:col-span-12 flex gap-2">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('piutang_utang') }}"
                   class="rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Piutang</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['total_piutang']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Item: {{ number_format($summary['count_piutang']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Sisa Piutang</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['sisa_piutang']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Terbayar: {{ $rupiah($summary['paid_piutang']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Utang</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['total_utang']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Item: {{ number_format($summary['count_utang']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Sisa Utang</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['sisa_utang']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Terbayar: {{ $rupiah($summary['paid_utang']) }}</p>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Piutang --}}
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Daftar Jatuh Tempo Piutang</p>
            <p class="text-xs text-slate-500 mt-0.5">Urut berdasarkan jatuh tempo</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">ID</th>
                            <th class="py-2 text-left font-medium">Nama</th>
                            <th class="py-2 text-left font-medium">Invoice</th>
                            <th class="py-2 text-right font-medium">Jatuh Tempo</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                            <th class="py-2 text-right font-medium">Dibayar</th>
                            <th class="py-2 text-right font-medium">Sisa</th>
                            <th class="py-2 text-right font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($piutang as $r)
                            <tr class="border-b border-slate-100">
                                <td class="py-2 font-medium">{{ $r['id'] }}</td>
                                <td class="py-2">{{ $r['nama'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['invoice'] }}</td>
                                <td class="py-2 text-right">{{ $r['jatuh_tempo'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($r['nominal']) }}</td>
                                <td class="py-2 text-right">{{ $rupiah($r['dibayar']) }}</td>
                                <td class="py-2 text-right">{{ $rupiah($sisa($r)) }}</td>
                                <td class="py-2 text-right">{!! $statusPill($r['status']) !!}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Utang --}}
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Daftar Jatuh Tempo Utang</p>
            <p class="text-xs text-slate-500 mt-0.5">Urut berdasarkan jatuh tempo</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">ID</th>
                            <th class="py-2 text-left font-medium">Nama</th>
                            <th class="py-2 text-left font-medium">Invoice</th>
                            <th class="py-2 text-right font-medium">Jatuh Tempo</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                            <th class="py-2 text-right font-medium">Dibayar</th>
                            <th class="py-2 text-right font-medium">Sisa</th>
                            <th class="py-2 text-right font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utang as $r)
                            <tr class="border-b border-slate-100">
                                <td class="py-2 font-medium">{{ $r['id'] }}</td>
                                <td class="py-2">{{ $r['nama'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['invoice'] }}</td>
                                <td class="py-2 text-right">{{ $r['jatuh_tempo'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($r['nominal']) }}</td>
                                <td class="py-2 text-right">{{ $rupiah($r['dibayar']) }}</td>
                                <td class="py-2 text-right">{{ $rupiah($sisa($r)) }}</td>
                                <td class="py-2 text-right">{!! $statusPill($r['status']) !!}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
