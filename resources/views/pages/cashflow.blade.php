@extends('layouts.app')

@section('title','Cashflow')
@section('header','Cashflow')
@section('subheader','Kas masuk/keluar, pengeluaran terbesar, profit sederhana')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');

    $pillNet = function($net) use ($rupiah) {
        if ($net >= 0) {
            return '<span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Net + '.$rupiah($net).'</span>';
        }
        return '<span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700">Net - '.$rupiah(abs($net)).'</span>';
    };
@endphp

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <form method="GET" action="{{ route('cashflow') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
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
                <button class="flex-1 rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('cashflow') }}"
                   class="flex-1 rounded-xl bg-slate-100 text-slate-700 px-4 py-2 text-sm font-semibold text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Kas Masuk</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['kas_masuk']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Penjualan + piutang cair + lainnya</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Kas Keluar</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['kas_keluar']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Belanja stok + operasional + lainnya</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Net Cashflow</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['net']) }}</p>
            <div class="mt-2">{!! $pillNet($summary['net']) !!}</div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Profit Sederhana</p>
            <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['profit']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Omzet - HPP - Opex</p>
        </div>
    </div>

    {{-- Profit detail + Top pengeluaran --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Rincian Profit Sederhana</p>
            <p class="text-xs text-slate-500 mt-0.5">Versi cepat untuk kontrol usaha</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-600">Omzet</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($summary['omzet']) }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-600">HPP (perkiraan)</td>
                            <td class="py-2 text-right font-semibold">- {{ $rupiah($summary['hpp']) }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-600">Opex (operasional)</td>
                            <td class="py-2 text-right font-semibold">- {{ $rupiah($summary['opex']) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold">Profit Sederhana</td>
                            <td class="py-2 text-right font-semibold">{{ $rupiah($summary['profit']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-xs text-slate-500">
                Next step: profit bisa dibuat lebih akurat (alokasi diskon, retur, biaya admin marketplace, dll).
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Pengeluaran Terbesar</p>
            <p class="text-xs text-slate-500 mt-0.5">Top kategori pengeluaran</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Kategori</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                            <th class="py-2 text-right font-medium">Porsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPengeluaran as $r)
                            <tr class="border-b border-slate-100">
                                <td class="py-2 font-medium">{{ $r['kategori'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($r['nominal']) }}</td>
                                <td class="py-2 text-right">{{ number_format($r['porsi_pct'], 0) }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kas masuk/keluar tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Kas Masuk</p>
            <p class="text-xs text-slate-500 mt-0.5">Daftar penerimaan kas</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Tanggal</th>
                            <th class="py-2 text-left font-medium">Sumber</th>
                            <th class="py-2 text-left font-medium">Kategori</th>
                            <th class="py-2 text-left font-medium">Metode</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                            <th class="py-2 text-left font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasMasukRows as $r)
                            <tr class="border-b border-slate-100">
                                <td class="py-2">{{ $r['tanggal'] }}</td>
                                <td class="py-2 font-medium">{{ $r['sumber'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['metode'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($r['nominal']) }}</td>
                                <td class="py-2 text-slate-600">{{ $r['catatan'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <p class="text-sm font-semibold">Kas Keluar</p>
            <p class="text-xs text-slate-500 mt-0.5">Daftar pengeluaran kas</p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Tanggal</th>
                            <th class="py-2 text-left font-medium">Tujuan</th>
                            <th class="py-2 text-left font-medium">Kategori</th>
                            <th class="py-2 text-left font-medium">Metode</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                            <th class="py-2 text-left font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasKeluarRows as $r)
                            <tr class="border-b border-slate-100">
                                <td class="py-2">{{ $r['tanggal'] }}</td>
                                <td class="py-2 font-medium">{{ $r['tujuan'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['kategori'] }}</td>
                                <td class="py-2 text-slate-600">{{ $r['metode'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($r['nominal']) }}</td>
                                <td class="py-2 text-slate-600">{{ $r['catatan'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-slate-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
