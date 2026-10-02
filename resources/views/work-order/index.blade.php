@extends('layouts.app')
@section('title', 'Work Order')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Work Order</h1>
    @can('wo.create')
    <a href="{{ route('work-order.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
        + Buat WO
    </a>
    @endcan
</div>

<form method="GET" class="flex gap-2 mb-4 flex-wrap">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor / nopol..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Semua Status</option>
        @foreach(['estimasi' => 'Estimasi', 'proses' => 'Proses', 'menunggu_pembayaran' => 'Menunggu Bayar', 'lunas' => 'Lunas', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
        <option value="{{ $val }}" {{ ($filters['status'] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-3 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Cari</button>
    <a href="{{ route('work-order.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:underline">Reset</a>
</form>

@php
$statusBadge = ['estimasi' => 'secondary', 'proses' => 'default', 'menunggu_pembayaran' => 'warning', 'lunas' => 'success', 'dibatalkan' => 'danger'];
$statusLabel = ['estimasi' => 'Estimasi', 'proses' => 'Proses', 'menunggu_pembayaran' => 'Menunggu Bayar', 'lunas' => 'Lunas', 'dibatalkan' => 'Dibatalkan'];
@endphp

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">No. WO</th>
                <th class="px-4 py-3 text-left">Kendaraan</th>
                <th class="px-4 py-3 text-left">Pelanggan</th>
                <th class="px-4 py-3 text-left">Mekanik</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($workOrders as $wo)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-medium">{{ $wo->nomor }}</td>
                <td class="px-4 py-3 font-medium">{{ $wo->kendaraan?->nomor_polisi }}</td>
                <td class="px-4 py-3">{{ $wo->pelanggan?->nama }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $wo->mekanik?->name }}</td>
                <td class="px-4 py-3 text-center">
                    <x-badge :variant="$statusBadge[$wo->status] ?? 'secondary'" :value="$statusLabel[$wo->status] ?? $wo->status" />
                </td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($wo->total, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $wo->tanggal_masuk->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('work-order.show', $wo) }}" class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada work order</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="flex items-center justify-between mt-4 text-sm text-gray-500">
    <span>Total: {{ $workOrders->total() }} work order</span>
    {{ $workOrders->withQueryString()->links() }}
</div>
@endsection
