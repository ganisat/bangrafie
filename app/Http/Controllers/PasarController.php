<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PasarController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));

        // Dummy ringkasan per pasar (nanti ganti query DB GROUP BY market/channel)
        $rows = [
            ['pasar'=>'Offline','trx'=>420,'omzet'=>52500000,'aov'=>125000,'profit_est'=>10200000],
            ['pasar'=>'Online','trx'=>210,'omzet'=>31800000,'aov'=>151000,'profit_est'=>7200000],
            ['pasar'=>'WA','trx'=>95,'omzet'=>12400000,'aov'=>131000,'profit_est'=>2600000],
            ['pasar'=>'Marketplace','trx'=>160,'omzet'=>28600000,'aov'=>179000,'profit_est'=>5100000],
        ];

        $totalOmzet = array_sum(array_column($rows, 'omzet'));
        $totalTrx   = array_sum(array_column($rows, 'trx'));

        // Tambah porsi %
        foreach ($rows as &$r) {
            $r['porsi_omzet_pct'] = $totalOmzet > 0 ? ($r['omzet'] / $totalOmzet) * 100 : 0;
            $r['porsi_trx_pct']   = $totalTrx > 0 ? ($r['trx'] / $totalTrx) * 100 : 0;
        }
        unset($r);

        // Dummy “top produk per pasar”
        $topProdukPerPasar = [
            'Offline' => [
                ['sku'=>'SKU-001','nama'=>'Kopi Arabica 250g','qty'=>120,'omzet'=>8400000],
                ['sku'=>'SKU-005','nama'=>'Roti Tawar','qty'=>95,'omzet'=>3800000],
            ],
            'Online' => [
                ['sku'=>'SKU-002','nama'=>'Gula Aren 500g','qty'=>80,'omzet'=>4400000],
                ['sku'=>'SKU-003','nama'=>'Susu UHT 1L','qty'=>75,'omzet'=>4100000],
            ],
            'WA' => [
                ['sku'=>'SKU-004','nama'=>'Teh Melati 100g','qty'=>50,'omzet'=>2000000],
            ],
            'Marketplace' => [
                ['sku'=>'SKU-001','nama'=>'Kopi Arabica 250g','qty'=>90,'omzet'=>6300000],
                ['sku'=>'SKU-002','nama'=>'Gula Aren 500g','qty'=>70,'omzet'=>3850000],
            ],
        ];

        $summary = [
            'start' => $start,
            'end'   => $end,
            'total_omzet' => $totalOmzet,
            'total_trx'   => $totalTrx,
        ];

        return view('pages.pasar', compact('summary','rows','topProdukPerPasar'));
    }
}
