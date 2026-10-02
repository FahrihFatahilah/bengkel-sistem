@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Supplier</h1>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Tambah Supplier</button>
</div>
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <button type="submit" class="px-3 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Cari</button>
</form>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Kontak</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Total PO</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($supplier as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $s->nama }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $s->kontak ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $s->email ?? '-' }}</td>
                <td class="px-4 py-3 text-center">{{ $s->pembelian_count }}</td>
                <td class="px-4 py-3 text-center">
                    <x-badge :variant="$s->is_active ? 'default' : 'secondary'" :value="$s->is_active ? 'Aktif' : 'Nonaktif'" />
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('supplier.show', $s) }}" class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data supplier</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $supplier->withQueryString()->links() }}</div>

<div id="modal-tambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah Supplier</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('supplier.store') }}" class="p-6 space-y-3">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                <input type="text" name="kontak" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
