<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $sale->receipt_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0;
            padding: 10px;
        }
        .receipt {
            text-align: center;
            border: 1px dashed #333;
            padding: 10px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }
        .receipt-number {
            font-weight: bold;
            font-size: 14px;
        }
        .date {
            font-size: 12px;
            margin-top: 5px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 7px 0;
            font-size: 12px;
            text-align: left;
        }
        .item-total {
            min-width: 70px;
            text-align: right;
        }
        .total-section {
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            margin: 10px 0;
            padding: 10px 0;
            font-weight: bold;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .grand-total {
            font-size: 16px;
            margin: 10px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $subtotal = $receiptLines->sum(fn ($line) => (float) $line->unit_price * $line->sold);
        $discount = $receiptLines->sum('discount_amount');
        $vat = $receiptLines->sum('vat_amount');
        $total = $receiptLines->sum('total_amount');
    @endphp

    <div class="receipt">
        <div class="header">
            <div style="font-size: 16px; font-weight: bold;">{{ config('app.name', 'AB Inventory') }}</div>
            <div class="receipt-number">Receipt: {{ $sale->receipt_number }}</div>
            <div class="date">{{ $sale->created_at->format('M d, Y h:i A') }}</div>
        </div>

        <div style="margin: 15px 0;">
            @foreach($receiptLines as $line)
                <div class="item-row">
                    <div>
                        <strong>{{ $line->product->product_name }}</strong><br>
                        {{ $line->sold }} x PHP {{ number_format($line->unit_price, 2) }}
                    </div>
                    <div class="item-total">
                        PHP {{ number_format($line->unit_price * $line->sold, 2) }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal</span>
                <span>PHP {{ number_format($subtotal, 2) }}</span>
            </div>
            @if($discount > 0)
                <div class="total-row">
                    <span>Discount</span>
                    <span>-PHP {{ number_format($discount, 2) }}</span>
                </div>
            @endif
            <div class="total-row">
                <span>VAT ({{ $sale->vat_rate }}%)</span>
                <span>PHP {{ number_format($vat, 2) }}</span>
            </div>
        </div>

        <div class="grand-total">
            TOTAL: PHP {{ number_format($total, 2) }}
        </div>

        <div style="margin: 15px 0; font-size: 12px;">
            <strong>Payment:</strong> {{ $sale->payment_mode_label }}<br>
            <strong>Cashier:</strong> {{ $sale->employee->user->name ?? 'N/A' }}<br>
            @if($sale->customer && $sale->customer->name != 'Walk-in')
                <strong>Customer:</strong> {{ $sale->customer->name }}<br>
            @endif
        </div>

        <div class="footer">
            <div style="margin: 10px 0;">Thank you for your purchase!</div>
            <div>{{ now()->format('M d, Y h:i A') }}</div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
