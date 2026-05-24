<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary</title>
    <style>
        body { font-family: Arial, sans-serif; color: #3f2417; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #6f3f2f; color: white; }
    </style>
</head>
<body onload="window.print()">
    <h1>Sales Summary</h1>
    <p>{{ $dateFrom }} to {{ $dateTo }}</p>
    <h2>Total Sales: PHP {{ number_format($totalSales, 2) }}</h2>
    <table>
        <tr><th>Period</th><th>Transactions</th><th>Total</th></tr>
        @foreach($salesByPeriod as $row)
            <tr><td>{{ $row->label }}</td><td>{{ $row->transactions }}</td><td>PHP {{ number_format($row->total, 2) }}</td></tr>
        @endforeach
    </table>
</body>
</html>
