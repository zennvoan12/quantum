@extends('layouts.app')

@section('title', 'Analisis Apriori')

@section('content')
<h1 class="text-2xl font-bold mb-2">Analisis Asosiasi Apriori</h1>
<p class="text-sm text-gray-500 mb-6">
    {{ $total }} transaksi dianalisis · Support(A→B) = frek(A∪B)/N · Confidence = Sup(A∪B)/Sup(A) · Lift = Confidence/P(B) · Lift &gt; 1 = asosiasi kuat
</p>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3 text-left">Aturan</th>
                <th class="px-6 py-3 text-center">Frek</th>
                <th class="px-6 py-3 text-right">Support</th>
                <th class="px-6 py-3 text-right">Confidence</th>
                <th class="px-6 py-3 text-right">Lift</th>
                <th class="px-6 py-3 text-center">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rules as $r)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium">{{ $r['a'] }} → {{ $r['b'] }}</td>
                    <td class="px-6 py-3 text-center">{{ $r['freq'] }}</td>
                    <td class="px-6 py-3 text-right">{{ number_format($r['support'], 2) }}%</td>
                    <td class="px-6 py-3 text-right">{{ number_format($r['confidence'], 2) }}%</td>
                    <td class="px-6 py-3 text-right font-mono">{{ number_format($r['lift'], 3) }}</td>
                    <td class="px-6 py-3 text-center">
                        @if ($r['lift'] > 1)
                            <span class="inline-block rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs font-semibold">Kuat</span>
                        @else
                            <span class="inline-block rounded-full bg-red-100 text-red-600 px-2 py-0.5 text-xs font-semibold">Lemah</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<p class="mt-4 text-xs text-gray-500">Aturan dengan frekuensi &lt; 3 disembunyikan sebagai noise.</p>
@endsection
