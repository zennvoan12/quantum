@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')

@section('dashboard-content')
<h1 class="text-xl uppercase tracking-[0.25em] font-light mb-8">Pesanan Saya</h1>

<!-- Filter Tabs -->
<div class="flex gap-8 mb-8 border-b border-neutral-200 text-[11px] uppercase tracking-[0.2em] text-neutral-500">
    <a href="?status=all" class="pb-3 {{ request('status', 'all') == 'all' ? 'text-neutral-900 border-b border-neutral-900 font-bold' : '' }}">Semua</a>
    <a href="?status=pending" class="pb-3 {{ request('status') == 'pending' ? 'text-neutral-900 border-b border-neutral-900 font-bold' : '' }}">Proses</a>
    <a href="?status=completed" class="pb-3 {{ request('status') == 'completed' ? 'text-neutral-900 border-b border-neutral-900 font-bold' : '' }}">Selesai</a>
</div>

<div class="space-y-4">
    @forelse($transaksis as $t)
        <div class="border border-neutral-200 rounded-lg p-5">
            <div class="flex justify-between items-center mb-4 text-[10px] uppercase tracking-[0.1em]">
                <span class="text-neutral-500">{{ $t->invoice_no }}</span>
                <span class="px-2 py-0.5 rounded {{ $t->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-neutral-100' }}">
                    {{ strtoupper($t->status) }}
                </span>
            </div>
            
            @foreach($t->items as $item)
                <div class="flex gap-4 mb-2">
                    <img src="{{ asset('images/'.$item->product->image) }}" class="w-12 h-12 object-cover rounded">
                    <div class="text-sm">{{ $item->product->name }} <span class="text-neutral-500 text-xs">x{{ $item->qty }}</span></div>
                </div>
            @endforeach
            
            <div class="border-t mt-4 pt-3 flex justify-between items-center">
                <span class="text-sm font-bold">Rp {{ number_format($t->total, 0, ',', '.') }}</span>
                <a href="{{ route('pelanggan.transaksi.show', $t->id) }}" class="text-[10px] uppercase tracking-[0.1em] border border-neutral-900 px-3 py-1.5 hover:bg-neutral-900 hover:text-white">Detail</a>
            </div>
        </div>
    @empty
        <div class="text-neutral-500 text-center py-12">Belum ada pesanan.</div>
    @endforelse
</div>
@endsection
