<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('/fonts/DejaVuSans.ttf');
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .info-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .info-section h3 {
            background: #f4f4f4;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 130px;
            padding: 4px 10px 4px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 4px 0;
            vertical-align: top;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: auto;
        }
        table.items thead {
            display: table-header-group;
        }
        table.items tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.items th {
            background: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }
        table.items td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .totals {
            margin-top: 15px;
            text-align: right;
            page-break-inside: avoid;
        }
        .totals table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals td {
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
            font-size: 15px;
            background: #f4f4f4;
        }
        .total-row td {
            padding: 10px;
            border-bottom: 2px solid #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice #{{ $invoice->invoice_number }}</h1>
        <p>Issue Date: {{ $invoice->created_at->format('Y-m-d') }}</p>
    </div>

    <div class="info-section">
        <h3>Order Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Order Number:</div>
                <div class="info-value">{{ $order->order_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Order Date:</div>
                <div class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Order Status:</div>
                <div class="info-value">{{ ucfirst($order->status) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">{{ ucfirst($order->payment_method) }}</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $order->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $order->user->email }}</div>
            </div>
            @if($order->user->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $order->user->phone }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($order->address)
    <div class="info-section">
        <h3>Shipping Address</h3>
        <div class="info-grid">
            @if($order->address->name)
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $order->address->name }}</div>
            </div>
            @endif
            @if($order->address->city)
            <div class="info-row">
                <div class="info-label">City:</div>
                <div class="info-value">{{ $order->address->city->name ?? 'Unknown' }}</div>
            </div>
            @endif
            @if($order->address->street)
            <div class="info-row">
                <div class="info-label">Street:</div>
                <div class="info-value">{{ $order->address->street }}</div>
            </div>
            @endif
            @if($order->address->building_number)
            <div class="info-row">
                <div class="info-label">Building:</div>
                <div class="info-value">{{ $order->address->building_number }}</div>
            </div>
            @endif
            @if($order->address->details)
            <div class="info-row">
                <div class="info-label">Details:</div>
                <div class="info-value">{{ $order->address->details }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="info-section">
        <h3>Order Items</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>Design</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->design->name ?? 'Design #' . $item->design_id }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                @if($order->subtotal)
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">${{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @endif
                @if($order->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td style="text-align: right;">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Amount:</td>
                    <td style="text-align: right;">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if($order->notes)
    <div class="info-section">
        <h3>Notes</h3>
        <p style="padding: 5px 0;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business</p>
        <p>{{ config('app.name') }} &copy; {{ now()->year }}</p>
    </div>
</body>
</html>
