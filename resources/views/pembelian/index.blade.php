@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Pembelian</h1>
    @can('pembelian.create')
    <a href="{{ route('pembelian.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Tambah Pembelian</a>
    @endcan
</div>
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor PO..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <select name="supplier_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Semua Supplier</option>
        @foreach($supplier as $s)
        <option value="{{ $s->id }}" {{ ($filters['supplier_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-3 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Cari</button>
</form>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nomor</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">Ref PO</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-left">Oleh</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pembelian as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-medium">{{ $p->nomor }}</td>
                <td class="px-4 py-3">{{ $p->supplier?->nama }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->referensi_po ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->user?->name }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('pembelian.show', $p) }}" class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada data pembelian</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="flex items-center justify-between mt-4 text-sm text-gray-500">
    <span>Total: {{ $pembelian->total() }}</span>
    {{ $pembelian->withQueryString()->links() }}
</div>
@endsection
