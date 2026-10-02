@extends('layouts.app')
@section('title', 'Paket Servis')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Paket Servis</h1>
    <a href="#" onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
       class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Tambah</a>
</div>
<div class="space-y-4">
    @forelse($paket as $p)
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <span class="font-semibold">{{ $p->nama }}</span>
                <span class="ml-3 text-sm text-blue-600 font-medium">Rp {{ number_format($p->harga_paket, 0, ',', '.') }}</span>
            </div>
            <x-badge :variant="$p->is_active ? 'default' : 'secondary'" :value="$p->is_active ? 'Aktif' : 'Nonaktif'" />
        </div>
        @if($p->detail->isNotEmpty())
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr><th class="px-4 py-2 text-left">Item</th><th class="px-4 py-2 text-right">Qty</th><th class="px-4 py-2 text-right">Harga</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($p->detail as $d)
                <tr>
                    <td class="px-4 py-2">{{ $d->barang?->nama ?? $d->tarifJasa?->nama ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $d->qty }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-400">Belum ada paket servis</div>
    @endforelse
</div>

<div id="modal-tambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah Paket Servis</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400">✕</button>
        </div>
        <form method="POST" action="{{ route('master.paket.store') }}" class="p-6 space-y-3">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket *</label>
                <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Harga Paket *</label>
                <input type="number" name="harga_paket" required min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
