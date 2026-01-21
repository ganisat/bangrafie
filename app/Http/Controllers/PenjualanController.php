<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->get('mode', 'harian'); // harian | mingguan
        $start = $request->get('start', Carbon::today()->subDays(6)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));

        $startDt = Carbon::parse($start)->startOfDay();
        $endDt   = Carbon::parse($end)->endOfDay();

        // ===== Dummy Tren =====
        $trendRows = [];
        $cursor = $startDt->copy();

        if ($mode === 'mingguan') {
            // group per minggu (ISO week)
            $seen = [];
            while ($cursor->lte($endDt)) {
                $year = $cursor->isoWeekYear;
                $week = $cursor->isoWeek;
                $key = $year.'-W'.str_pad($week, 2, '0', STR_PAD_LEFT);

                if (!isset($seen[$key])) {
                    $weekStart = $cursor->copy()->startOfWeek(Carbon::MONDAY);
                    $weekEnd   = $cursor->copy()->endOfWeek(Carbon::SUNDAY);

                    // clamp to range
                    if ($weekStart->lt($startDt)) $weekStart = $startDt->copy();
                    if ($weekEnd->gt($endDt))     $weekEnd   = $endDt->copy();

                    $omzet = rand(8000000, 25000000);
                    $trx   = rand(60, 220);
                    $aov   = (int) round($omzet / max($trx, 1));

                    $trendRows[] = [
                        'label' => $key,
                        'range' => $weekStart->format('d M').' - '.$weekEnd->format('d M'),
                        'omzet' => $omzet,
                        'trx'   => $trx,
                        'aov'   => $aov,
                    ];
                    $seen[$key] = true;
                }
                $cursor->addDay();
            }
        } else {
            // harian
            while ($cursor->lte($endDt)) {
                $omzet = rand(600000, 4500000);
                $trx   = rand(8, 60);
                $aov   = (int) round($omzet / max($trx, 1));

                $trendRows[] = [
                    'label' => $cursor->format('Y-m-d'),
                    'range' => $cursor->format('d M Y'),
                    'omzet' => $omzet,
                    'trx'   => $trx,
                    'aov'   => $aov,
                ];
                $cursor->addDay();
            }
        }

        // ===== Dummy Perbandingan WoW & MoM (ringkas) =====
        // WoW: 7 hari terakhir vs 7 hari sebelumnya
        $woW_this_start = Carbon::parse($end)->subDays(6)->startOfDay();
        $woW_this_end   = Carbon::parse($end)->endOfDay();
        $woW_prev_start = $woW_this_start->copy()->subDays(7);
        $woW_prev_end   = $woW_this_end->copy()->subDays(7);

        $wow = $this->fakeComparePeriod($woW_prev_start, $woW_prev_end, $woW_this_start, $woW_this_end);

        // MoM: bulan ini (MTD) vs bulan lalu (periode sama)
        $mtd_this_start = Carbon::now()->startOfMonth()->startOfDay();
        $mtd_this_end   = Carbon::now()->endOfDay();
        $daysIntoMonth  = $mtd_this_start->diffInDays($mtd_this_end) + 1;

        $mtd_prev_start = Carbon::now()->subMonthNoOverflow()->startOfMonth()->startOfDay();
        $mtd_prev_end   = $mtd_prev_start->copy()->addDays($daysIntoMonth - 1)->endOfDay();

        $mom = $this->fakeComparePeriod($mtd_prev_start, $mtd_prev_end, $mtd_this_start, $mtd_this_end);

        return view('pages.penjualan', compact(
            'mode','start','end','trendRows','wow','mom',
            'woW_prev_start','woW_prev_end','woW_this_start','woW_this_end',
            'mtd_prev_start','mtd_prev_end','mtd_this_start','mtd_this_end'
        ));
    }

    private function fakeComparePeriod(Carbon $prevStart, Carbon $prevEnd, Carbon $thisStart, Carbon $thisEnd): array
    {
        // Dummy angka—nanti ganti jadi query SUM omzet, COUNT transaksi, dll.
        $prevOmzet = rand(10000000, 45000000);
        $prevTrx   = rand(150, 700);
        $prevAov   = (int) round($prevOmzet / max($prevTrx, 1));

        $thisOmzet = (int) round($prevOmzet * (rand(85, 125) / 100));
        $thisTrx   = (int) round($prevTrx * (rand(85, 125) / 100));
        $thisAov   = (int) round($thisOmzet / max($thisTrx, 1));

        return [
            'prev' => ['omzet' => $prevOmzet, 'trx' => $prevTrx, 'aov' => $prevAov],
            'this' => ['omzet' => $thisOmzet, 'trx' => $thisTrx, 'aov' => $thisAov],
            'delta' => [
                'omzet' => $this->delta($prevOmzet, $thisOmzet),
                'trx'   => $this->delta($prevTrx, $thisTrx),
                'aov'   => $this->delta($prevAov, $thisAov),
            ],
        ];
    }

    private function delta(int $prev, int $curr): array
    {
        $diff = $curr - $prev;
        $pct  = $prev > 0 ? ($diff / $prev) * 100 : 0;

        return [
            'diff' => $diff,
            'pct'  => $pct,
            'up'   => $diff >= 0,
        ];
    }
}
