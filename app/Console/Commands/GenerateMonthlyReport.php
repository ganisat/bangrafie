<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateMonthlyReport extends Command
{
    protected $signature = 'reports:monthly {--month=} {--type=ringkas}';
    protected $description = 'Generate laporan bulanan otomatis (PDF + CSV) ke storage/app/reports';

    public function handle()
    {
        $type = (string) $this->option('type') ?: 'ringkas';

        // default: bulan lalu (laporan bulanan biasanya untuk bulan yang sudah selesai)
        $monthOpt = $this->option('month');
        $month = $monthOpt ? Carbon::parse($monthOpt.'-01') : Carbon::now()->subMonthNoOverflow()->startOfMonth();

        $start = $month->copy()->startOfMonth()->format('Y-m-d');
        $end   = $month->copy()->endOfMonth()->format('Y-m-d');

        $data = $this->buildReportData($start, $end, $type);

        Storage::disk('local')->makeDirectory('reports');

        $ym = $month->format('Y-m');
        $base = "reports/{$ym}_laporan_{$type}";

        // CSV
        $csvPath = "{$base}.csv";
        $csv = "\xEF\xBB\xBF";
        $csv .= "Tanggal,Transaksi,Omzet,HPP (estimasi),Opex,Profit sederhana\n";
        foreach ($data['rows'] as $r) {
            $csv .= implode(',', [
                $r['tanggal'],
                $r['trx'],
                $r['omzet'],
                $r['hpp'],
                $r['opex'],
                $r['profit'],
            ]) . "\n";
        }
        $csv .= "\nRINGKASAN\n";
        $csv .= "Total Transaksi,{$data['summary']['total_trx']}\n";
        $csv .= "Total Omzet,{$data['summary']['total_omzet']}\n";
        $csv .= "Total Profit,{$data['summary']['total_profit']}\n";
        Storage::disk('local')->put($csvPath, $csv);

        // PDF
        $pdfPath = "{$base}.pdf";
        $pdf = Pdf::loadView('reports.laporan-pdf', [
            'start' => $start,
            'end'   => $end,
            'type'  => $type,
            'data'  => $data,
        ])->setPaper('A4', 'portrait');
        Storage::disk('local')->put($pdfPath, $pdf->output());

        $this->info("OK: {$csvPath} & {$pdfPath}");
        return 0;
    }

    private function buildReportData(string $start, string $end, string $type): array
    {
        // Sama seperti controller (dummy).
        $dates = [];
        $cur = Carbon::parse($start);
        $endDt = Carbon::parse($end);

        while ($cur->lte($endDt)) {
            $trx   = rand(10, 60);
            $omzet = rand(800000, 4500000);
            $hpp   = (int) round($omzet * (rand(55, 75) / 100));
            $opex  = rand(50000, 350000);
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

        return [
            'rows' => $dates,
            'summary' => [
                'total_trx'    => array_sum(array_column($dates, 'trx')),
                'total_omzet'  => array_sum(array_column($dates, 'omzet')),
                'total_profit' => array_sum(array_column($dates, 'profit')),
            ],
        ];
    }
}
