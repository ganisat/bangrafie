@extends('layouts.app')

@section('title','Laporan')
@section('header','Laporan')
@section('subheader','Export PDF/Excel dan laporan bulanan otomatis')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter + Export --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('laporan') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Tanggal Mulai</label>
                <input type="date" name="start" value="{{ $start }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Tanggal Selesai</label>
                <input type="date" name="end" value="{{ $end }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="text-xs text-slate-600">Jenis laporan</label>
                <select name="type" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option value="ringkas" {{ $type==='ringkas' ? 'selected' : '' }}>Ringkas</option>
                    <option value="detail" {{ $type==='detail' ? 'selected' : '' }}>Detail (placeholder)</option>
                </select>
            </div>

            <div class="md:col-span-12 flex flex-wrap gap-2">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Terapkan</button>

                <a class="rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold"
                   href="{{ route('laporan') }}">Reset</a>

                <a class="rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm font-semibold"
                   href="{{ route('laporan.export.excel', ['start'=>$start,'end'=>$end,'type'=>$type]) }}">
                    Export Excel (CSV)
                </a>

                <a class="rounded-xl bg-rose-600 text-white px-4 py-2 text-sm font-semibold"
                   href="{{ route('laporan.export.pdf', ['start'=>$start,'end'=>$end,'type'=>$type]) }}">
                    Export PDF
                </a>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Transaksi</p>
                <p class="font-semibold mt-1">{{ number_format($data['summary']['total_trx']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Omzet</p>
                <p class="font-semibold mt-1">{{ $rupiah($data['summary']['total_omzet']) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Total Profit</p>
                <p class="font-semibold mt-1">{{ $rupiah($data['summary']['total_profit']) }}</p>
            </div>
        </div>
    </div>

    {{-- Preview tabel laporan --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-sm font-semibold">Preview Laporan</p>
        <p class="text-xs text-slate-500 mt-0.5">Periode {{ $start }} s/d {{ $end }}</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                <tr class="border-b border-slate-200">
                    <th class="py-2 text-left font-medium">Tanggal</th>
                    <th class="py-2 text-right font-medium">Transaksi</th>
                    <th class="py-2 text-right font-medium">Omzet</th>
                    <th class="py-2 text-right font-medium">HPP</th>
                    <th class="py-2 text-right font-medium">Opex</th>
                    <th class="py-2 text-right font-medium">Profit</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['rows'] as $r)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 font-medium">{{ $r['tanggal'] }}</td>
                        <td class="py-2 text-right">{{ number_format($r['trx']) }}</td>
                        <td class="py-2 text-right font-semibold">{{ $rupiah($r['omzet']) }}</td>
                        <td class="py-2 text-right">{{ $rupiah($r['hpp']) }}</td>
                        <td class="py-2 text-right">{{ $rupiah($r['opex']) }}</td>
                        <td class="py-2 text-right font-semibold">{{ $rupiah($r['profit']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar laporan bulanan otomatis yang tersimpan --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-sm font-semibold">Arsip Laporan Bulanan (Otomatis)</p>
        <p class="text-xs text-slate-500 mt-0.5">File tersimpan di storage/app/reports</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500">
                <tr class="border-b border-slate-200">
                    <th class="py-2 text-left font-medium">File</th>
                    <th class="py-2 text-right font-medium">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($files as $f)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 font-medium">{{ $f }}</td>
                        <td class="py-2 text-right text-slate-600">
                            (download via storage/manual)
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-6 text-center text-slate-500">Belum ada arsip</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-xs text-slate-500">
            Next step: bisa dibuat tombol “Download” dengan route khusus yang aman (signed URL).
        </div>
    </div>

</div>
@endsection
