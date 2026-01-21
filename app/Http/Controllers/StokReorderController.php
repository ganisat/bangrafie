<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StokReorderController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $filter   = $request->get('filter', 'all'); // all | kritis | normal
        $sort     = $request->get('sort', 'stok_asc'); // stok_asc | stok_desc | nama_asc
        $minRule  = (int) $request->get('min_rule', 0); // override rule minimum stok (0 = pakai per item)
        $coverDay = (int) $request->get('cover_day', 14); // target cover hari (buat saran reorder)

        // ===== Dummy inventory (nanti diganti DB) =====
        $items = [
            ['sku'=>'SKU-001','nama'=>'Kopi Arabica 250g','kategori'=>'Minuman','stok'=>8,'min_stok'=>12,'hpp'=>45000,'lead_time_hari'=>3,'avg_daily_sold'=>1.2],
            ['sku'=>'SKU-002','nama'=>'Gula Aren 500g','kategori'=>'Bahan','stok'=>25,'min_stok'=>10,'hpp'=>28000,'lead_time_hari'=>4,'avg_daily_sold'=>0.6],
            ['sku'=>'SKU-003','nama'=>'Susu UHT 1L','kategori'=>'Minuman','stok'=>5,'min_stok'=>15,'hpp'=>15000,'lead_time_hari'=>2,'avg_daily_sold'=>2.0],
            ['sku'=>'SKU-004','nama'=>'Teh Melati 100g','kategori'=>'Minuman','stok'=>40,'min_stok'=>20,'hpp'=>12000,'lead_time_hari'=>5,'avg_daily_sold'=>0.3],
            ['sku'=>'SKU-005','nama'=>'Roti Tawar','kategori'=>'Makanan','stok'=>3,'min_stok'=>6,'hpp'=>9000,'lead_time_hari'=>1,'avg_daily_sold'=>1.5],
            ['sku'=>'SKU-006','nama'=>'Biskuit Coklat','kategori'=>'Snack','stok'=>35,'min_stok'=>30,'hpp'=>7000,'lead_time_hari'=>7,'avg_daily_sold'=>0.1],
        ];

        // Apply search
        if ($q !== '') {
            $items = array_values(array_filter($items, function($r) use ($q) {
                return str_contains(mb_strtolower($r['nama']), mb_strtolower($q))
                    || str_contains(mb_strtolower($r['sku']), mb_strtolower($q))
                    || str_contains(mb_strtolower($r['kategori']), mb_strtolower($q));
            }));
        }

        // Add computed fields
        foreach ($items as &$r) {
            $min = $minRule > 0 ? $minRule : (int)$r['min_stok'];
            $r['min_eff'] = $min;
            $r['status'] = ($r['stok'] <= $min) ? 'kritis' : 'normal';

            // simple reorder recommendation:
            // target stock = coverDay * avg_daily_sold
            // need = max(0, target - current)
            $target = (int) ceil(max(0, $coverDay) * (float)$r['avg_daily_sold']);
            $need   = max(0, $target - (int)$r['stok']);

            // add buffer for lead time (optional simple): lead_time * avg_daily_sold
            $buffer = (int) ceil((int)$r['lead_time_hari'] * (float)$r['avg_daily_sold']);
            $need2  = max(0, ($target + $buffer) - (int)$r['stok']);

            $r['target_stock'] = $target;
            $r['reorder_qty']  = $need2;
            $r['reorder_cost'] = $r['reorder_qty'] * (int)$r['hpp'];
        }
        unset($r);

        // Filter status
        if ($filter === 'kritis') {
            $items = array_values(array_filter($items, fn($r) => $r['status'] === 'kritis'));
        } elseif ($filter === 'normal') {
            $items = array_values(array_filter($items, fn($r) => $r['status'] === 'normal'));
        }

        // Sort
        usort($items, function($a,$b) use ($sort) {
            return match ($sort) {
                'stok_desc' => $b['stok'] <=> $a['stok'],
                'nama_asc'  => strcmp($a['nama'], $b['nama']),
                default     => $a['stok'] <=> $b['stok'], // stok_asc
            };
        });

        // Derive sections
        $stokKritis = array_values(array_filter($items, fn($r) => $r['status'] === 'kritis'));

        $saranReorder = array_values(array_filter($items, function($r){
            return $r['reorder_qty'] > 0;
        }));

        // summary
        $summary = [
            'total_item' => count($items),
            'kritis'     => count($stokKritis),
            'butuh_reorder' => count($saranReorder),
            'estimasi_modal_reorder' => array_sum(array_column($saranReorder, 'reorder_cost')),
            'cover_day'  => $coverDay,
            'min_rule'   => $minRule,
        ];

        return view('pages.stok-reorder', compact(
            'items','stokKritis','saranReorder',
            'q','filter','sort','minRule','coverDay','summary'
        ));
    }
}
