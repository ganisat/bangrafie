<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));
        $type  = $request->get('type', 'ringkas'); // ringkas | detail

        // Dummy dataset (nanti ganti query DB)
        $data = $this->buildReportData($start, $end, $type);

        // List file laporan bulanan yang sudah dibuat (storage/app/reports)
        $files = Storage::disk('local')->exists('reports')
            ? collect(Storage::disk('local')->files('reports'))
                ->filter(fn($p) => str_ends_with($p, '.pdf') || str_ends_with($p, '.csv'))
                ->sortDesc()
                ->values()
                ->all()
            : [];

        return view('pages.laporan', compact('start','end','type','data','files'));
    }

    public function exportExcel(Request $request)
    {
        $start = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));
        $type  = $request->get('type', 'ringkas');

        $data = $this->buildReportData($start, $end, $type);

        // Excel-friendly: CSV
        $filename = "laporan_{$type}_{$start}_sd_{$end}.csv";

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM biar Excel Windows tidak berantakan
            fwrite($out, "\xEF\xBB\xBF");

            // Header
            fputcsv($out, ['Tanggal', 'Transaksi', 'Omzet', 'HPP (estimasi)', 'Opex', 'Profit sederhana']);

            foreach ($data['rows'] as $r) {
                fputcsv($out, [
                    $r['tanggal'],
                    $r['trx'],
                    $r['omzet'],
                    $r['hpp'],
                    $r['opex'],
                    $r['profit'],
                ]);
            }

            // Summary di bawah
            fputcsv($out, []);
            fputcsv($out, ['RINGKASAN']);
            fputcsv($out, ['Total Transaksi', $data['summary']['total_trx']]);
            fputcsv($out, ['Total Omzet', $data['summary']['total_omzet']]);
            fputcsv($out, ['Total Profit', $data['summary']['total_profit']]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $start = $request->get('start', Carbon::today()->subDays(30)->format('Y-m-d'));
        $end   = $request->get('end', Carbon::today()->format('Y-m-d'));
        $type  = $request->get('type', 'ringkas');

        $data = $this->buildReportData($start, $end, $type);

        $pdf = Pdf::loadView('reports.laporan-pdf', [
            'start' => $start,
            'end'   => $end,
            'type'  => $type,
            'data'  => $data,
        ])->setPaper('A4', 'portrait');

        $filename = "laporan_{$type}_{$start}_sd_{$end}.pdf";
        return $pdf->download($filename);
    }

    private function buildReportData(string $start, string $end, string $type): array
    {
        // Dummy “laporan penjualan harian”
        // Nanti ganti: query transaksi GROUP BY date, join hpp, opex, dst.
        $dates = [];
        $cur = Carbon::parse($start);
        $endDt = Carbon::parse($end);

        while ($cur->lte($endDt)) {
            $trx   = rand(10, 60);
            $omzet = rand(800000, 4500000);
            $hpp   = (int) round($omzet * (rand(55, 75) / 100));     // estimasi HPP 55-75%
            $opex  = rand(50000, 350000);                            // pengeluaran operasional harian
            $profit = $omzet - $hpp - $opex;

            $dates[] = [
                'tanggal' => $cur->format('Y-m-d'),
                'trx'     => $trx,
                'omzet'   => $omzet,
                'hpp'     => $hpp,
                'opex'    => $opex,
                'profit'  => $profit,
            ];

            $cur->addDay();
        }

        $summary = [
            'total_trx'    => array_sum(array_column($dates, 'trx')),
            'total_omzet'  => array_sum(array_column($dates, 'omzet')),
            'total_profit' => array_sum(array_column($dates, 'profit')),
        ];

        return [
            'rows' => $dates,
            'summary' => $summary,
        ];
    }
}
