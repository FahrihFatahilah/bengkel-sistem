@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1 class="text-xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card title="Total Barang" :value="$stats['total_barang']" color="blue" />
    <x-stat-card title="Stok Kritis" :value="$stats['stok_kritis']" color="orange" />
    <x-stat-card title="WO Proses" :value="$stats['wo_proses']" color="blue" />
    <x-stat-card title="Menunggu Bayar" :value="$stats['wo_menunggu_bayar']" color="green" />
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Stok Kritis --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold">⚠️ Stok Kritis</h2>
            <a href="{{ route('laporan.stok-minimum') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500">
                <tr>
                    <th class="px-4 py-2 text-left">Barang</th>
                    <th class="px-4 py-2 text-right">Stok</th>
                    <th class="px-4 py-2 text-right">Min</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokKritis as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <a href="{{ route('barang.show', $b) }}" class="font-medium hover:text-blue-600">{{ $b->nama }}</a>
                        <p class="text-xs text-gray-400">{{ $b->kode }}</p>
                    </td>
                    <td class="px-4 py-2 text-right font-medium text-orange-500">{{ $b->stok }}</td>
                    <td class="px-4 py-2 text-right text-gray-400">{{ $b->stok_minimum }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Tidak ada stok kritis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Antrian Pembayaran --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold">💳 Antrian Pembayaran</h2>
            <a href="{{ route('work-order.index', ['status' => 'menunggu_pembayaran']) }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500">
                <tr>
                    <th class="px-4 py-2 text-left">No. WO</th>
                    <th class="px-4 py-2 text-left">Kendaraan</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($antriPembayaran as $wo)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <a href="{{ route('work-order.show', $wo) }}" class="font-mono text-xs font-medium hover:text-blue-600">{{ $wo->nomor }}</a>
                        <p class="text-xs text-gray-400">{{ $wo->pelanggan?->nama }}</p>
                    </td>
                    <td class="px-4 py-2 font-medium">{{ $wo->kendaraan?->nomor_polisi }}</td>
                    <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($wo->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Tidak ada antrian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
