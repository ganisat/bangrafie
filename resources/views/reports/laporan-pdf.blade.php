<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .muted { color:#666; }
        table { width:100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border:1px solid #ddd; padding:6px; }
        th { background:#f5f5f5; text-align:left; }
        .right { text-align:right; }
    </style>
</head>
<body>
    <h2 style="margin:0;">Laporan {{ strtoupper($type) }}</h2>
    <p class="muted" style="margin:4px 0 0;">Periode: {{ $start }} s/d {{ $end }}</p>

    <table>
        <thead>
        <tr>
            <th>Tanggal</th>
            <th class="right">Transaksi</th>
            <th class="right">Omzet</th>
            <th class="right">HPP</th>
            <th class="right">Opex</th>
            <th class="right">Profit</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['rows'] as $r)
            <tr>
                <td>{{ $r['tanggal'] }}</td>
                <td class="right">{{ number_format($r['trx']) }}</td>
                <td class="right">{{ number_format($r['omzet']) }}</td>
                <td class="right">{{ number_format($r['hpp']) }}</td>
                <td class="right">{{ number_format($r['opex']) }}</td>
                <td class="right">{{ number_format($r['profit']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p style="margin-top:10px;">
        <strong>Ringkasan:</strong>
        Total Trx {{ number_format($data['summary']['total_trx']) }},
        Total Omzet {{ number_format($data['summary']['total_omzet']) }},
        Total Profit {{ number_format($data['summary']['total_profit']) }}
    </p>
</body>
</html>
