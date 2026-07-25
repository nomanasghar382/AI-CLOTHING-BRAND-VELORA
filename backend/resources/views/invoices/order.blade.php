<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
        h1 { font-size: 24px; margin-bottom: 4px; } table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; } th { background: #f5f5f5; }
        .right { text-align: right; } .muted { color: #666; }
    </style>
</head>
<body>
    <h1>VELORA Invoice</h1>
    <p class="muted">Invoice {{ $order->number }} · {{ $order->created_at?->format('Y-m-d') }}</p>
    <p><strong>Bill to:</strong><br>
        {{ $order->billingAddress?->first_name }} {{ $order->billingAddress?->last_name }}<br>
        {{ $order->billingAddress?->line1 }} {{ $order->billingAddress?->line2 }}<br>
        {{ $order->billingAddress?->city }}, {{ $order->billingAddress?->postal_code }} {{ $order->billingAddress?->country }}
    </p>
    <table>
        <thead><tr><th>Item</th><th>SKU</th><th class="right">Qty</th><th class="right">Price</th><th class="right">Total</th></tr></thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr><td>{{ $item->product_name }}</td><td>{{ $item->sku }}</td><td class="right">{{ $item->quantity }}</td><td class="right">{{ $order->currency }} {{ $item->unit_price }}</td><td class="right">{{ $order->currency }} {{ $item->line_total }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <table>
        <tr><td class="right">Subtotal</td><td class="right">{{ $order->currency }} {{ $order->subtotal }}</td></tr>
        <tr><td class="right">Discount</td><td class="right">-{{ $order->currency }} {{ $order->discount_total }}</td></tr>
        <tr><td class="right">Shipping</td><td class="right">{{ $order->currency }} {{ $order->shipping_total }}</td></tr>
        <tr><td class="right"><strong>Total</strong></td><td class="right"><strong>{{ $order->currency }} {{ $order->grand_total }}</strong></td></tr>
    </table>
</body>
</html>
