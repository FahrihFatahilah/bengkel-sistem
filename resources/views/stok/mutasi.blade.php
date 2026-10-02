@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('content')
<h1 class="text-xl font-semibold mb-6">Mutasi Stok Antar Rak</h1>
<div class="bg-white rounded-lg border border-gray-200 max-w-lg">
    <div class="px-6 py-4 border-b border-gray-100"><h2 class="text-sm font-semibold">Form Mutasi</h2></div>
    <form method="POST" action="{{ route('stok.mutasi.store') }}" class="p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Barang *</label>
            <select name="barang_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Pilih Barang --</option>
                @foreach($barang as $b)
                <option value="{{ $b->id }}">{{ $b->kode }} — {{ $b->nama }} (stok: {{ $b->stok }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Rak *</label>
                <select name="dari_lokasi_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Rak --</option>
                    @foreach($rak as $r)
                    <option value="{{ $r->id }}">{{ $r->kode }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ke Rak *</label>
                <select name="ke_lokasi_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Rak --</option>
                    @foreach($rak as $r)
                    <option value="{{ $r->id }}">{{ $r->kode }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Qty *</label>
            <input type="number" name="qty" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <input type="text" name="catatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Proses Mutasi</button>
    </form>
</div>
@endsection
