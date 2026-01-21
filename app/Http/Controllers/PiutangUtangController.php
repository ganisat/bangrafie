<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PiutangUtangController extends Controller
{
    public function index(Request $request)
    {
        $start  = $request->get('start', Carbon::today()->format('Y-m-01'));
        $end    = $request->get('end', Carbon::today()->addDays(30)->format('Y-m-d'));
        $status = $request->get('status', 'open'); // open | paid | all
        $type   = $request->get('type', 'all'); // all | piutang | utang

        // ===== Dummy Piutang =====
        $piutang = [
            ['id'=>'AR-001','nama'=>'Customer A','jatuh_tempo'=>'2026-01-25','invoice'=>'INV-1001','nominal'=>1500000,'dibayar'=>0,'status'=>'open','catatan'=>'Pembayaran termin'],
            ['id'=>'AR-002','nama'=>'Toko Sinar','jatuh_tempo'=>'2026-01-27','invoice'=>'INV-1008','nominal'=>800000,'dibayar'=>300000,'status'=>'open','catatan'=>'Sisa tagihan'],
            ['id'=>'AR-003','nama'=>'Customer B','jatuh_tempo'=>'2026-01-10','invoice'=>'INV-0990','nominal'=>1200000,'dibayar'=>1200000,'status'=>'paid','catatan'=>'Lunas'],
        ];

        // ===== Dummy Utang =====
        $utang = [
            ['id'=>'AP-001','nama'=>'Supplier B','jatuh_tempo'=>'2026-01-26','invoice'=>'BILL-2301','nominal'=>2100000,'dibayar'=>0,'status'=>'open','catatan'=>'Belanja stok'],
            ['id'=>'AP-002','nama'=>'Supplier C','jatuh_tempo'=>'2026-01-29','invoice'=>'BILL-2310','nominal'=>950000,'dibayar'=>500000,'status'=>'open','catatan'=>'Termin'],
            ['id'=>'AP-003','nama'=>'Supplier D','jatuh_tempo'=>'2026-01-05','invoice'=>'BILL-2202','nominal'=>1750000,'dibayar'=>1750000,'status'=>'paid','catatan'=>'Lunas'],
        ];

        // ===== Filter helper =====
        $inRange = function($row) use ($start,$end) {
            return $row['jatuh_tempo'] >= $start && $row['jatuh_tempo'] <= $end;
        };

        $applyStatus = function($rows) use ($status) {
            if ($status === 'all') return $rows;
            return array_values(array_filter($rows, fn($r) => $r['status'] === $status));
        };

        // Apply range
        $piutang = array_values(array_filter($piutang, $inRange));
        $utang   = array_values(array_filter($utang, $inRange));

        // Apply status
        $piutang = $applyStatus($piutang);
        $utang   = $applyStatus($utang);

        // Only type selection
        if ($type === 'piutang') $utang = [];
        if ($type === 'utang')   $piutang = [];

        // ===== Compute totals =====
        $sumNominal = fn($rows) => array_sum(array_column($rows, 'nominal'));
        $sumDibayar = fn($rows) => array_sum(array_column($rows, 'dibayar'));

        $totalPiutang = $sumNominal($piutang);
        $paidPiutang  = $sumDibayar($piutang);
        $sisaPiutang  = max(0, $totalPiutang - $paidPiutang);

        $totalUtang   = $sumNominal($utang);
        $paidUtang    = $sumDibayar($utang);
        $sisaUtang    = max(0, $totalUtang - $paidUtang);

        // Sort by due date
        usort($piutang, fn($a,$b) => strcmp($a['jatuh_tempo'], $b['jatuh_tempo']));
        usort($utang, fn($a,$b) => strcmp($a['jatuh_tempo'], $b['jatuh_tempo']));

        $summary = [
            'start' => $start,
            'end'   => $end,
            'status'=> $status,
            'type'  => $type,

            'total_piutang' => $totalPiutang,
            'paid_piutang'  => $paidPiutang,
            'sisa_piutang'  => $sisaPiutang,

            'total_utang'   => $totalUtang,
            'paid_utang'    => $paidUtang,
            'sisa_utang'    => $sisaUtang,

            'count_piutang' => count($piutang),
            'count_utang'   => count($utang),
        ];

        return view('pages.piutang-utang', compact('piutang','utang','summary'));
    }
}
