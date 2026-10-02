@extends('layouts.app')
@section('title', 'Stok Minimum')
@section('content')
<h1 class="text-xl font-semibold mb-6">⚠️ Laporan Stok Minimum</h1>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-right">Stok</th>
                <th class="px-4 py-3 text-right">Min</th>
                <th class="px-4 py-3 text-right">Kekurangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang as $b)
            <tr class="hover:bg-gray-50 bg-orange-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $b->kode }}</td>
                <td class="px-4 py-3 font-medium">
                    <a href="{{ route('barang.show', $b) }}" class="hover:text-blue-600">{{ $b->nama }}</a>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $b->kategori?->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $b->supplier_utama?->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-bold text-orange-500">{{ $b->stok }}</td>
                <td class="px-4 py-3 text-right text-gray-500">{{ $b->stok_minimum }}</td>
                <td class="px-4 py-3 text-right font-medium text-red-500">{{ $b->stok_minimum - $b->stok }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-green-600">✓ Semua stok aman</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
