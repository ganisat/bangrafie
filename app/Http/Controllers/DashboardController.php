<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'omzet_hari_ini'    => 1250000,
            'omzet_bulan_ini'   => 32500000,
            'transaksi'         => 42,
            'aov'               => 298000,
            'estimasi_profit'   => 7200000,
        ];

        $piutangJatuhTempo = [
            ['nama' => 'Toko Sinar', 'jatuh_tempo' => '2026-01-25', 'nominal' => 1500000],
            ['nama' => 'Customer A', 'jatuh_tempo' => '2026-01-27', 'nominal' => 800000],
        ];

        $utangJatuhTempo = [
            ['nama' => 'Supplier B', 'jatuh_tempo' => '2026-01-26', 'nominal' => 2100000],
            ['nama' => 'Supplier C', 'jatuh_tempo' => '2026-01-29', 'nominal' => 950000],
        ];

        return view('pages.dashboard', compact('summary', 'piutangJatuhTempo', 'utangJatuhTempo'));
    }
}
