@extends('layouts.app')
@section('title', 'Kartu Stok — ' . $barang->nama)
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('barang.show', $barang) }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">Kartu Stok — {{ $barang->nama }}</h1>
</div>
<form method="GET" class="flex gap-2 mb-4">
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600">Dari</label>
        <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600">Sampai</label>
        <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Filter</button>
</form>
@php
$tipeColor = ['masuk' => 'text-green-600', 'keluar' => 'text-red-600', 'mutasi_masuk' => 'text-blue-600', 'mutasi_keluar' => 'text-orange-600', 'koreksi' => 'text-purple-600', 'opname' => 'text-gray-600'];
$tipeLabel = ['masuk' => 'Masuk', 'keluar' => 'Keluar', 'mutasi_masuk' => 'Mutasi Masuk', 'mutasi_keluar' => 'Mutasi Keluar', 'koreksi' => 'Koreksi', 'opname' => 'Opname'];
@endphp
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-left">Lokasi</th>
                <th class="px-4 py-3 text-left">Oleh</th>
                <th class="px-4 py-3 text-right">Qty</th>
                <th class="px-4 py-3 text-right">Saldo Sebelum</th>
                <th class="px-4 py-3 text-right">Saldo Sesudah</th>
                <th class="px-4 py-3 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transaksi as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-xs text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-2"><span class="text-xs font-medium {{ $tipeColor[$t->tipe] ?? '' }}">{{ $tipeLabel[$t->tipe] ?? $t->tipe }}</span></td>
                <td class="px-4 py-2 text-xs text-gray-500">{{ $t->lokasiRak?->kode ?? '-' }}</td>
                <td class="px-4 py-2 text-xs">{{ $t->user?->name }}</td>
                <td class="px-4 py-2 text-right text-xs font-medium {{ $t->qty > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $t->qty > 0 ? '+' : '' }}{{ $t->qty }}
                </td>
                <td class="px-4 py-2 text-right text-xs">{{ $t->saldo_sebelum }}</td>
                <td class="px-4 py-2 text-right text-xs font-bold">{{ $t->saldo_sesudah }}</td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $t->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $transaksi->withQueryString()->links() }}</div>
@endsection
