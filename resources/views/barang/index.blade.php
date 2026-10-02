@extends('layouts.app')
@section('title', 'Barang')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Barang / Sparepart</h1>
    @can('barang.create')
    <a href="{{ route('barang.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
        + Tambah Barang
    </a>
    @endcan
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
           placeholder="Cari kode / nama..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <select name="kategori_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Semua Kategori</option>
        @foreach($kategori as $k)
        <option value="{{ $k->id }}" {{ ($filters['kategori_id'] ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
        @endforeach
    </select>
    <label class="flex items-center gap-1.5 text-sm text-gray-600">
        <input type="checkbox" name="stok_kritis" value="1" {{ ($filters['stok_kritis'] ?? '') ? 'checked' : '' }}> Stok Kritis
    </label>
    <button type="submit" class="px-3 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Cari</button>
    <a href="{{ route('barang.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:underline">Reset</a>
</form>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-right">Stok</th>
                <th class="px-4 py-3 text-right">Harga Jual</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang as $b)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $b->kode }}</td>
                <td class="px-4 py-3 font-medium">{{ $b->nama }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $b->kategori?->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-right {{ $b->stok <= $b->stok_minimum ? 'text-orange-500 font-semibold' : '' }}">
                    {{ $b->stok }} {{ $b->satuan }}
                    @if($b->stok <= $b->stok_minimum) ⚠️ @endif
                </td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    <x-badge :variant="$b->is_active ? 'default' : 'secondary'" :value="$b->is_active ? 'Aktif' : 'Nonaktif'" />
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-1 justify-end">
                        <a href="{{ route('barang.show', $b) }}" class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Detail</a>
                        @can('barang.edit')
                        <a href="{{ route('barang.edit', $b) }}" class="px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded">Edit</a>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada data barang</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="flex items-center justify-between mt-4 text-sm text-gray-500">
    <span>Total: {{ $barang->total() }} barang</span>
    {{ $barang->withQueryString()->links() }}
</div>
@endsection
