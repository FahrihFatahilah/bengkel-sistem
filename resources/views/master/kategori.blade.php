@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Kategori Barang</h1>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Tambah</button>
</div>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr><th class="px-4 py-3 text-left">Nama</th><th class="px-4 py-3 text-left">Deskripsi</th><th class="px-4 py-3 text-center">Jumlah Barang</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($kategori as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $k->nama }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $k->deskripsi ?? '-' }}</td>
                <td class="px-4 py-3 text-center">{{ $k->barang_count }}</td>
                <td class="px-4 py-3">
                    <button onclick="editKategori('{{ $k->id }}', '{{ $k->nama }}', '{{ $k->deskripsi }}')"
                            class="px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded">Edit</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada kategori</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="modal-tambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-sm shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah Kategori</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('master.kategori.store') }}" class="p-6 space-y-3">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <input type="text" name="deskripsi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-sm shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Edit Kategori</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="form-edit" method="POST" class="p-6 space-y-3">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="nama" id="edit-nama" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <input type="text" name="deskripsi" id="edit-deskripsi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
<script>
function editKategori(id, nama, deskripsi) {
    document.getElementById('form-edit').action = `/master/kategori/${id}`;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-deskripsi').value = deskripsi || '';
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
