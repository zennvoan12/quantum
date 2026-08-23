@extends('layouts.app')

@section('title', 'Analisis Apriori')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Analisis Asosiasi Apriori</h1>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
@endif
@if (session('errors'))
    <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('errors') }}</div>
@endif

<!-- Form Process -->
<div class="border border-neutral-200 rounded-lg p-6 mb-8">
    <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Jalankan Analisis Baru</h2>
    <form method="POST" action="{{ route('admin.apriori.process') }}" class="max-w-xl space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Minimum Support (0.01 - 1)</label>
                <input type="number" step="0.01" min="0.01" max="1" name="min_support" value="{{ old('min_support', 0.02) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Minimum Confidence (0.01 - 1)</label>
                <input type="number" step="0.01" min="0.01" max="1" name="min_confidence" value="{{ old('min_confidence', 0.5) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
            </div>
        </div>
        <button type="submit" class="border border-neutral-900 bg-neutral-900 text-white px-6 py-2 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Proses Apriori</button>
    </form>
</div>

<!-- History Logs -->
<div class="border border-neutral-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                    <th class="py-3 pr-4 font-normal">Tanggal</th>
                    <th class="py-3 pr-4 font-normal">Min Support</th>
                    <th class="py-3 pr-4 font-normal">Min Confidence</th>
                    <th class="py-3 pr-4 font-normal">Total Aturan</th>
                    <th class="py-3 font-normal"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-b border-neutral-100">
                        <td class="py-3 pr-4">{{ $log->run_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4">{{ $log->min_support }}</td>
                        <td class="py-3 pr-4">{{ $log->min_confidence }}</td>
                        <td class="py-3 pr-4">{{ $log->total_rules }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.apriori.show', $log) }}" class="underline underline-offset-4 text-neutral-500 hover:text-neutral-900">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-neutral-400">Belum ada analisis.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-neutral-200">{{ $logs->links() }}</div>
</div>
@endsection