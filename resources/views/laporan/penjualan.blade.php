@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
<h1 class="text-xl font-semibold mb-6">Laporan Penjualan</h1>
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

<div class="grid grid-cols-3 gap-4 mb-6">
    <x-stat-card title="Total Transaksi" :value="$summary['total_transaksi']" color="blue" />
    <x-stat-card title="Total Pendapatan" value="Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}" color="green" />
    <x-stat-card title="Total Diskon" value="Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}" color="orange" />
</div>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">No. WO</th>
                <th class="px-4 py-3 text-left">Pelanggan</th>
                <th class="px-4 py-3 text-left">Kendaraan</th>
                <th class="px-4 py-3 text-left">Kasir</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-right">Diskon</th>
                <th class="px-4 py-3 text-right">Bayar</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($workOrders as $wo)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-mono text-xs">
                    <a href="{{ route('work-order.show', $wo) }}" class="hover:text-blue-600">{{ $wo->nomor }}</a>
                </td>
                <td class="px-4 py-2">{{ $wo->pelanggan?->nama }}</td>
                <td class="px-4 py-2">{{ $wo->kendaraan?->nomor_polisi }}</td>
                <td class="px-4 py-2 text-gray-500">{{ $wo->kasir?->name }}</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($wo->total, 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-right text-red-500">{{ $wo->diskon > 0 ? '- Rp ' . number_format($wo->diskon, 0, ',', '.') : '-' }}</td>
                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($wo->total_bayar, 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $wo->tanggal_selesai?->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada data penjualan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
