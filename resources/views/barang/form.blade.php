@extends('layouts.app')
@section('title', isset($barang) ? 'Edit Barang' : 'Tambah Barang')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('barang.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">{{ isset($barang) ? 'Edit Barang' : 'Tambah Barang' }}</h1>
</div>

<div class="bg-white rounded-lg border border-gray-200 max-w-2xl">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold">Informasi Barang</h2>
    </div>
    <form method="POST" action="{{ isset($barang) ? route('barang.update', $barang) : route('barang.store') }}" class="p-6 space-y-4">
        @csrf
        @if(isset($barang)) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode / SKU *</label>
                <input type="text" name="kode" value="{{ old('kode', $barang->kode ?? '') }}"
                       {{ isset($barang) ? 'readonly' : '' }}
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ isset($barang) ? 'bg-gray-50' : '' }}">
                @error('kode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan ?? 'pcs') }}"
                       placeholder="pcs, botol, set..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang *</label>
            <input type="text" name="nama" value="{{ old('nama', $barang->nama ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Utama</label>
                <select name="supplier_utama_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($supplier as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_utama_id', $barang->supplier_utama_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('harga_beli')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual *</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('harga_jual')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum ?? 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        @if(isset($barang))
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ $barang->is_active ? 'checked' : '' }}
                   class="rounded border-gray-300">
            <label for="is_active" class="text-sm text-gray-700">Barang aktif</label>
        </div>
        @endif

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
            <a href="{{ route('barang.index') }}" class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
