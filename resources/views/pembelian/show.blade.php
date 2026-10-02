@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('pembelian.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold font-mono">{{ $pembelian->nomor }}</h1>
</div>
<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Info Pembelian</h3>
        <div class="flex justify-between"><span class="text-gray-500">Supplier</span><span class="font-medium">{{ $pembelian->supplier?->nama }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ $pembelian->tanggal->format('d M Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Ref PO</span><span>{{ $pembelian->referensi_po ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Dibuat oleh</span><span>{{ $pembelian->user?->name }}</span></div>
        <div class="flex justify-between font-bold"><span>Total</span><span>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span></div>
    </div>
    @if($pembelian->catatan)
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm">
        <h3 class="font-semibold mb-2">Catatan</h3>
        <p class="text-gray-600">{{ $pembelian->catatan }}</p>
    </div>
    @endif
</div>
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold">Detail Item</h2></div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Barang</th>
                <th class="px-4 py-2 text-right">Qty</th>
                <th class="px-4 py-2 text-right">Harga Beli</th>
                <th class="px-4 py-2 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($pembelian->detail as $d)
            <tr>
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $d->barang?->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $d->barang?->kode }}</p>
                </td>
                <td class="px-4 py-3 text-right">{{ $d->qty }} {{ $d->barang?->satuan }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="border-t border-gray-200 bg-gray-50">
            <tr>
                <td colspan="3" class="px-4 py-3 text-right font-bold">Total</td>
                <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
