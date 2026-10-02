@extends('layouts.app')
@section('title', $pelanggan->nama)
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('pelanggan.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">{{ $pelanggan->nama }}</h1>
</div>
<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Info Pelanggan</h3>
        <div class="flex justify-between"><span class="text-gray-500">No. HP</span><span>{{ $pelanggan->no_hp ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Alamat</span><span>{{ $pelanggan->alamat ?? '-' }}</span></div>
    </div>
</div>

@foreach($pelanggan->kendaraan as $k)
<div class="bg-white rounded-lg border border-gray-200 mb-4">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <div>
            <span class="font-bold text-blue-600">{{ $k->nomor_polisi }}</span>
            <span class="text-sm text-gray-500 ml-2">{{ $k->merek }} {{ $k->tipe }} {{ $k->tahun }}</span>
        </div>
        @can('wo.create')
        <a href="{{ route('work-order.create') }}" class="text-xs text-blue-600 hover:underline">+ Buat WO</a>
        @endcan
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500">
            <tr>
                <th class="px-4 py-2 text-left">No. WO</th>
                <th class="px-4 py-2 text-left">Keluhan</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-right">Total</th>
                <th class="px-4 py-2 text-left">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($k->workOrder as $wo)
            @php $statusBadge = ['estimasi' => 'secondary', 'proses' => 'default', 'menunggu_pembayaran' => 'warning', 'lunas' => 'success', 'dibatalkan' => 'danger']; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2">
                    <a href="{{ route('work-order.show', $wo) }}" class="font-mono text-xs font-medium hover:text-blue-600">{{ $wo->nomor }}</a>
                </td>
                <td class="px-4 py-2 text-gray-600 text-xs">{{ Str::limit($wo->keluhan, 50) }}</td>
                <td class="px-4 py-2 text-center">
                    <x-badge :variant="$statusBadge[$wo->status] ?? 'secondary'" :value="$wo->status" />
                </td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($wo->total, 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $wo->tanggal_masuk->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-4 text-center text-gray-400 text-xs">Belum ada riwayat servis</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Tambah Kendaraan --}}
<div class="mt-2">
    <button onclick="document.getElementById('modal-kendaraan').classList.remove('hidden')"
            class="text-sm text-blue-600 hover:underline">+ Tambah Kendaraan</button>
</div>
@endforeach

@if($pelanggan->kendaraan->isEmpty())
<div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-400">
    Belum ada kendaraan terdaftar.
    <button onclick="document.getElementById('modal-kendaraan').classList.remove('hidden')"
            class="block mx-auto mt-2 text-sm text-blue-600 hover:underline">+ Tambah Kendaraan</button>
</div>
@endif

{{-- Modal Tambah Kendaraan --}}
<div id="modal-kendaraan" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah Kendaraan</h3>
            <button onclick="document.getElementById('modal-kendaraan').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('pelanggan.kendaraan.store', $pelanggan) }}" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Polisi *</label>
                <input type="text" name="nomor_polisi" required placeholder="B 1234 ABC"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merek</label>
                    <input type="text" name="merek" placeholder="Honda, Yamaha..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <input type="text" name="tipe" placeholder="Beat, Vario..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number" name="tahun" min="1990" max="{{ date('Y') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-kendaraan').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
