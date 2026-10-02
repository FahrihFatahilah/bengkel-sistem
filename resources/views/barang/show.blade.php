@extends('layouts.app')
@section('title', $barang->nama)
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('barang.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">{{ $barang->nama }}</h1>
    @can('barang.edit')
    <a href="{{ route('barang.edit', $barang) }}" class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">
        ✏️ Edit
    </a>
    @endcan
</div>

<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-2 text-sm">
        <h3 class="font-semibold text-gray-700 mb-3">Info Barang</h3>
        <div class="flex justify-between"><span class="text-gray-500">Kode</span><span class="font-mono">{{ $barang->kode }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kategori</span><span>{{ $barang->kategori?->nama ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Satuan</span><span>{{ $barang->satuan }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Supplier</span><span>{{ $barang->supplier_utama?->nama ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Status</span>
            <x-badge :variant="$barang->is_active ? 'default' : 'secondary'" :value="$barang->is_active ? 'Aktif' : 'Nonaktif'" />
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-2 text-sm">
        <h3 class="font-semibold text-gray-700 mb-3">Harga</h3>
        <div class="flex justify-between"><span class="text-gray-500">Harga Beli</span><span>Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Harga Jual</span><span class="font-semibold">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</span></div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-2 text-sm">
        <h3 class="font-semibold text-gray-700 mb-3">Stok</h3>
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Stok Saat Ini</span>
            <span class="text-2xl font-bold {{ $barang->stok <= $barang->stok_minimum ? 'text-orange-500' : 'text-gray-900' }}">
                {{ $barang->stok }} <span class="text-sm font-normal">{{ $barang->satuan }}</span>
            </span>
        </div>
        <div class="flex justify-between"><span class="text-gray-500">Stok Minimum</span><span>{{ $barang->stok_minimum }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Nilai Stok</span><span>Rp {{ number_format($barang->stok * $barang->harga_beli, 0, ',', '.') }}</span></div>
    </div>
</div>

{{-- Kartu Stok --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold">Kartu Stok (50 terakhir)</h2>
        <a href="{{ route('stok.kartu', $barang) }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-left">Tipe</th>
                <th class="px-4 py-2 text-left">Oleh</th>
                <th class="px-4 py-2 text-right">Qty</th>
                <th class="px-4 py-2 text-right">Saldo</th>
                <th class="px-4 py-2 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @php
            $tipeColor = ['masuk' => 'text-green-600', 'keluar' => 'text-red-600', 'mutasi_masuk' => 'text-blue-600', 'mutasi_keluar' => 'text-orange-600', 'koreksi' => 'text-purple-600', 'opname' => 'text-gray-600'];
            $tipeLabel = ['masuk' => 'Masuk', 'keluar' => 'Keluar', 'mutasi_masuk' => 'Mutasi Masuk', 'mutasi_keluar' => 'Mutasi Keluar', 'koreksi' => 'Koreksi', 'opname' => 'Opname'];
            @endphp
            @forelse($barang->stokTransaksi as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-xs text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-2"><span class="text-xs font-medium {{ $tipeColor[$t->tipe] ?? '' }}">{{ $tipeLabel[$t->tipe] ?? $t->tipe }}</span></td>
                <td class="px-4 py-2 text-xs">{{ $t->user?->name }}</td>
                <td class="px-4 py-2 text-right text-xs font-medium {{ $t->qty > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $t->qty > 0 ? '+' : '' }}{{ $t->qty }}
                </td>
                <td class="px-4 py-2 text-right font-bold">{{ $t->saldo_sesudah }}</td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $t->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
