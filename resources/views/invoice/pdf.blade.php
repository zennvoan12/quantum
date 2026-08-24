<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 1px solid #ddd; padding-bottom: 20px; }
        .logo { font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-info { text-align: right; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th, .table td { border-bottom: 1px solid #eee; padding: 10px; text-align: left; }
        .table th { background: #f9f9f9; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #777; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { width: 300px; float: right; }
        .summary td { padding: 6px 10px; border: none; }
        .summary tr.total { font-weight: bold; border-top: 1px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">Quantum Cell</div>
            <p>Analisis Keranjang Belanja & E-Commerce</p>
        </div>
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <p><strong>No:</strong> {{ $order->invoice_no }}</p>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Status:</strong> {{ strtoupper($order->status) }}</p>
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <strong>Kepada Yth:</strong><br>
        {{ $order->user->name ?? 'Pelanggan' }}<br>
        {{ $order->user->email ?? '-' }}<br>
        @if($order->alamat)
            <br><strong>Alamat Pengiriman:</strong><br>{{ $order->alamat }}
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Subtotal Produk</td>
            <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>PPN ({{ $order->tax_rate ?? 11 }}%)</td>
            <td class="text-right">Rp {{ number_format($order->tax_amount ?? ($order->total * 0.11), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Ongkos Kirim</td>
            <td class="text-right">Gratis</td>
        </tr>
        <tr class="total">
            <td>Total Bayar</td>
            <td class="text-right">Rp {{ number_format($order->total_paid ?? ($order->total * 1.11), 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>