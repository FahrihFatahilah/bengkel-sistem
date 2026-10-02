@extends('layouts.app')
@section('title', 'Laporan Pembelian')
@section('content')
<h1 class="text-xl font-semibold mb-6">Laporan Pembelian</h1>
<form method="GET" class="flex gap-2 mb-6 flex-wrap">
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600">Dari</label>
        <input type="date" name="dari" value="{{ $filters['dari'] }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600">Sampai</label>
        <input type="date" name="sampai" value="{{ $filters['sampai'] }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Filter</button>
</form>
<div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-sm">
    Total Pembelian: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
</div>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nomor</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pembelian as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-mono text-xs">
                    <a href="{{ route('pembelian.show', $p) }}" class="hover:text-blue-600">{{ $p->nomor }}</a>
                </td>
                <td class="px-4 py-2">{{ $p->supplier?->nama }}</td>
                <td class="px-4 py-2 text-gray-500">{{ $p->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada data pembelian</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
