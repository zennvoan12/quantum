@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="fade-up">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-8">Riwayat Transaksi</h1>

    @if ($transaksis->isEmpty())
        <div class="border border-neutral-200 rounded-lg p-12 text-center">
            <p class="text-neutral-500 mb-4">Belum ada riwayat transaksi.</p>
            <a href="{{ route('produk') }}" class="border border-neutral-900 text-neutral-900 py-3 px-8 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-100 inline-block">Mulai Belanja</a>
        </div>
    @else
        <div class="overflow-x-auto border border-neutral-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Item</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksis as $trx)
                        <tr class="border-b border-neutral-100 hover:bg-neutral-50">
                            <td class="py-3 px-4 font-mono text-xs">{{ $trx->invoice_no }}</td>
                            <td class="py-3 px-4">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                @foreach ($trx->items as $item)
                                    <div class="truncate">{{ $item->product->name }} x{{ $item->qty }}</div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase tracking-[0.1em]
                                    @if($trx->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($trx->status === 'paid') bg-green-100 text-green-800
                                    @elseif($trx->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($trx->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($trx->status === 'completed') bg-emerald-100 text-emerald-800
                                    @elseif($trx->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-neutral-100 text-neutral-800 @endif">
                                    {{ ucfirst($trx->status) }}
                                </span>
                                @if($trx->payment)
                                    <br><span class="text-[10px] text-neutral-400 mt-1">{{ ucfirst($trx->payment->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pelanggan.transaksi.show', $trx) }}" class="text-[11px] underline hover:text-neutral-900">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $transaksis->links() }}
    @endif
</div>
@endsection