<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Production Report</title>
    <style>
        body { font-family: Arial, sans-serif; color: #3f2417; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #6f3f2f; color: white; }
    </style>
</head>
<body onload="window.print()">
    <h1>Production Report</h1>
    <p>{{ $dateFrom }} to {{ $dateTo }} | Type: {{ strtoupper($type) }}</p>
    <h2>IN: {{ $totalProductionIn }} | OUT: {{ $totalProductionOut }}</h2>
    <table>
        <tr><th>Date</th><th>Type</th><th>Quantity</th></tr>
        @foreach($productionByPeriod as $row)
            <tr><td>{{ $row->date }}</td><td>{{ $row->type }}</td><td>{{ $row->quantity }}</td></tr>
        @endforeach
    </table>
</body>
</html>
