@extends('layouts.app')

@section('title', 'Beranda')
@section('header', 'Beranda (Ringkasan)')
@section('subheader', 'Omzet, transaksi, AOV, dan estimasi profit')

@php
    $rupiah = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-6">

        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Omzet Hari Ini</p>
                <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['omzet_hari_ini']) }}</p>
                <p class="text-xs text-slate-500 mt-2">Update realtime (dummy)</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Omzet Bulan Ini</p>
                <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['omzet_bulan_ini']) }}</p>
                <p class="text-xs text-slate-500 mt-2">MTD</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Transaksi</p>
                <p class="text-lg font-semibold mt-1">{{ number_format($summary['transaksi']) }}</p>
                <p class="text-xs text-slate-500 mt-2">Jumlah order</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">AOV</p>
                <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['aov']) }}</p>
                <p class="text-xs text-slate-500 mt-2">Rata-rata per transaksi</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">Estimasi Profit</p>
                <p class="text-lg font-semibold mt-1">{{ $rupiah($summary['estimasi_profit']) }}</p>
                <p class="text-xs text-slate-500 mt-2">Sederhana (dummy)</p>
            </div>
        </div>

        {{-- Quick sections --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold">Piutang Jatuh Tempo</p>
                        <p class="text-xs text-slate-500 mt-0.5">Prioritaskan penagihan</p>
                    </div>
                    <a href="{{ route('piutang_utang') }}" class="text-sm font-medium text-slate-900 underline underline-offset-4">
                        Lihat semua
                    </a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Nama</th>
                            <th class="py-2 text-left font-medium">Jatuh Tempo</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($piutangJatuhTempo as $row)
                            <tr class="border-b border-slate-100">
                                <td class="py-2">{{ $row['nama'] }}</td>
                                <td class="py-2">{{ $row['jatuh_tempo'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($row['nominal']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-500">Belum ada data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold">Utang Jatuh Tempo</p>
                        <p class="text-xs text-slate-500 mt-0.5">Atur pembayaran supplier</p>
                    </div>
                    <a href="{{ route('piutang_utang') }}" class="text-sm font-medium text-slate-900 underline underline-offset-4">
                        Lihat semua
                    </a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="py-2 text-left font-medium">Nama</th>
                            <th class="py-2 text-left font-medium">Jatuh Tempo</th>
                            <th class="py-2 text-right font-medium">Nominal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($utangJatuhTempo as $row)
                            <tr class="border-b border-slate-100">
                                <td class="py-2">{{ $row['nama'] }}</td>
                                <td class="py-2">{{ $row['jatuh_tempo'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $rupiah($row['nominal']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-500">Belum ada data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Placeholder area for charts --}}
        <div class="rounded-2xl bg-white border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold">Ringkasan Grafik</p>
                    <p class="text-xs text-slate-500 mt-0.5">Nanti bisa isi tren penjualan harian/mingguan</p>
                </div>
                <a href="{{ route('penjualan') }}" class="text-sm font-medium text-slate-900 underline underline-offset-4">
                    Buka Penjualan
                </a>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="h-28 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 text-sm">
                    Placeholder Chart 1
                </div>
                <div class="h-28 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 text-sm">
                    Placeholder Chart 2
                </div>
                <div class="h-28 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 text-sm">
                    Placeholder Chart 3
                </div>
            </div>
        </div>

    </div>
@endsection
