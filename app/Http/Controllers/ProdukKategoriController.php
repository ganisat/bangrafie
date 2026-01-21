<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ProdukKategoriController extends Controller
{
    public function index(Request $request)
    {
        $start   = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end     = $request->get('end', Carbon::today()->format('Y-m-d'));
        $by      = $request->get('by', 'qty'); // qty | omzet
        $channel = $request->get('channel', 'all'); // all | offline | online | wa | marketplace

        // ===== Dummy Top Produk =====
        $topProduk = [
            ['sku'=>'SKU-001','nama'=>'Kopi Arabica 250g','kategori'=>'Minuman','qty'=>220,'omzet'=>15400000,'aov'=>70000,'margin_pct'=>22],
            ['sku'=>'SKU-002','nama'=>'Gula Aren 500g','kategori'=>'Bahan','qty'=>180,'omzet'=>9900000,'aov'=>55000,'margin_pct'=>18],
            ['sku'=>'SKU-003','nama'=>'Susu UHT 1L','kategori'=>'Minuman','qty'=>160,'omzet'=>8800000,'aov'=>55000,'margin_pct'=>12],
            ['sku'=>'SKU-004','nama'=>'Teh Melati 100g','kategori'=>'Minuman','qty'=>140,'omzet'=>5600000,'aov'=>40000,'margin_pct'=>25],
            ['sku'=>'SKU-005','nama'=>'Roti Tawar','kategori'=>'Makanan','qty'=>120,'omzet'=>4800000,'aov'=>40000,'margin_pct'=>15],
        ];

        // sort by qty/omzet
        usort($topProduk, function($a,$b) use ($by) {
            return ($b[$by] <=> $a[$by]);
        });

        // ===== Dummy Top Kategori =====
        $topKategori = [
            ['kategori'=>'Minuman','qty'=>520,'omzet'=>29800000,'produk_aktif'=>18],
            ['kategori'=>'Bahan','qty'=>260,'omzet'=>13200000,'produk_aktif'=>12],
            ['kategori'=>'Makanan','qty'=>190,'omzet'=>9800000,'produk_aktif'=>9],
            ['kategori'=>'Snack','qty'=>120,'omzet'=>6200000,'produk_aktif'=>14],
        ];
        usort($topKategori, fn($a,$b) => ($b[$by] <=> $a[$by]));

        // ===== Dummy Slow Moving =====
        // definisi sederhana: stok > 0 dan qty penjualan periode rendah
        $slowMoving = [
            ['sku'=>'SKU-101','nama'=>'Biskuit Coklat','kategori'=>'Snack','stok'=>35,'qty_periode'=>2,'omzet_periode'=>120000,'last_sold'=>'2026-01-05'],
            ['sku'=>'SKU-102','nama'=>'Saos Sambal 250ml','kategori'=>'Bahan','stok'=>20,'qty_periode'=>0,'omzet_periode'=>0,'last_sold'=>'2025-12-18'],
            ['sku'=>'SKU-103','nama'=>'Mie Instan Spesial','kategori'=>'Makanan','stok'=>48,'qty_periode'=>1,'omzet_periode'=>35000,'last_sold'=>'2026-01-02'],
            ['sku'=>'SKU-104','nama'=>'Sereal Jagung','kategori'=>'Makanan','stok'=>12,'qty_periode'=>0,'omzet_periode'=>0,'last_sold'=>'2025-12-01'],
        ];

        // Summary kecil
        $summary = [
            'periode' => $start.' s/d '.$end,
            'channel' => $channel,
            'total_produk_terjual' => array_sum(array_column($topProduk, 'qty')),
            'total_omzet_top' => array_sum(array_column($topProduk, 'omzet')),
            'slow_moving_count' => count($slowMoving),
        ];

        return view('pages.produk-kategori', compact(
            'start','end','by','channel',
            'topProduk','topKategori','slowMoving','summary'
        ));
    }
}
