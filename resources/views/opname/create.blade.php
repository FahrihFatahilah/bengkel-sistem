@extends('layouts.app')
@section('title', 'Buat Opname')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('opname.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">Buat Stock Opname</h1>
</div>
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100"><h2 class="text-sm font-semibold">Input Stok Fisik</h2></div>
    <form method="POST" action="{{ route('opname.store') }}" class="p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Barang</th>
                        <th class="px-4 py-2 text-right">Stok Sistem</th>
                        <th class="px-4 py-2 text-right w-36">Stok Fisik *</th>
                        <th class="px-4 py-2 text-left w-48">Lokasi Rak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($barang as $i => $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <input type="hidden" name="items[{{ $i }}][barang_id]" value="{{ $b->id }}">
                            <p class="font-medium">{{ $b->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $b->kode }}</p>
                        </td>
                        <td class="px-4 py-2 text-right font-medium">{{ $b->stok }} {{ $b->satuan }}</td>
                        <td class="px-4 py-2">
                            <input type="number" name="items[{{ $i }}][stok_fisik]" min="0" required
                                   value="{{ $b->stok }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-right">
                        </td>
                        <td class="px-4 py-2">
                            <select name="items[{{ $i }}][lokasi_rak_id]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                                <option value="">-- Pilih Rak --</option>
                                @foreach($rak as $r)
                                <option value="{{ $r->id }}">{{ $r->kode }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan Opname</button>
            <a href="{{ route('opname.index') }}" class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
