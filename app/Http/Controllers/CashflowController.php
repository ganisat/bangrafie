<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));

        // ===== Dummy summary (nanti ganti query DB) =====
        $kasMasuk  = 78500000;  // contoh: pembayaran penjualan + piutang cair + lain-lain
        $kasKeluar = 51200000;  // contoh: belanja stok + operasional + gaji + sewa

        $netCashflow = $kasMasuk - $kasKeluar;

        // ===== Dummy omzet & HPP untuk profit sederhana =====
        $omzet = 96500000;
        $hpp   = 61200000;

        // Pengeluaran operasional (subset dari kas keluar, dummy)
        $pengeluaranOperasional = 16800000;

        $profitSederhana = $omzet - $hpp - $pengeluaranOperasional;

        // ===== Dummy list kas masuk/keluar (tabel transaksi kas) =====
        $kasMasukRows = [
            ['tanggal'=>'2026-01-20','sumber'=>'Penjualan Offline','kategori'=>'Penjualan','metode'=>'Cash','nominal'=>4500000,'catatan'=>'Shift pagi'],
            ['tanggal'=>'2026-01-19','sumber'=>'Penjualan Online','kategori'=>'Penjualan','metode'=>'Transfer','nominal'=>3200000,'catatan'=>'Marketplace'],
            ['tanggal'=>'2026-01-18','sumber'=>'Pembayaran Piutang','kategori'=>'Piutang','metode'=>'Transfer','nominal'=>1500000,'catatan'=>'Customer A'],
        ];

        $kasKeluarRows = [
            ['tanggal'=>'2026-01-20','tujuan'=>'Supplier B','kategori'=>'Belanja Stok','metode'=>'Transfer','nominal'=>5200000,'catatan'=>'Restock susu'],
            ['tanggal'=>'2026-01-19','tujuan'=>'Biaya Operasional','kategori'=>'Listrik','metode'=>'Cash','nominal'=>750000,'catatan'=>'Token'],
            ['tanggal'=>'2026-01-18','tujuan'=>'Sewa','kategori'=>'Sewa','metode'=>'Transfer','nominal'=>3500000,'catatan'=>'Bulanan'],
        ];

        // ===== Dummy top expenses (pengeluaran terbesar) =====
        $topPengeluaran = [
            ['kategori'=>'Belanja Stok','nominal'=>21500000,'porsi_pct'=>42],
            ['kategori'=>'Sewa','nominal'=>8500000,'porsi_pct'=>17],
            ['kategori'=>'Gaji','nominal'=>7200000,'porsi_pct'=>14],
            ['kategori'=>'Listrik','nominal'=>2300000,'porsi_pct'=>5],
            ['kategori'=>'Transport','nominal'=>1800000,'porsi_pct'=>4],
        ];

        $summary = [
            'start' => $start,
            'end'   => $end,
            'kas_masuk' => $kasMasuk,
            'kas_keluar' => $kasKeluar,
            'net' => $netCashflow,
            'omzet' => $omzet,
            'hpp' => $hpp,
            'opex' => $pengeluaranOperasional,
            'profit' => $profitSederhana,
        ];

        return view('pages.cashflow', compact(
            'summary','kasMasukRows','kasKeluarRows','topPengeluaran'
        ));
    }
}
