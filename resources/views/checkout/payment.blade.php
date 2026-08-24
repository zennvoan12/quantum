@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Pembayaran Pesanan #{{ $order->invoice_no }}</h1>

<div class="max-w-2xl mx-auto">
    <div class="border border-neutral-200 rounded-lg p-6 mb-8 text-center bg-neutral-50">
        <p class="text-sm text-neutral-500 mb-2">Total yang harus dibayar:</p>
        <p class="text-2xl font-bold text-neutral-900 mb-6">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
        
        <button id="pay-button" class="border border-neutral-900 bg-neutral-900 text-white px-8 py-3 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 transition-colors">
            Bayar Sekarang via Midtrans
        </button>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                window.location.href = "{{ route('checkout.success', $order) }}";
            },
            onPending: function(result){
                window.location.href = "{{ route('checkout.success', $order) }}";
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                alert('Anda menutup popup sebelum menyelesaikan pembayaran');
            }
        });
    };
</script>
@endsection