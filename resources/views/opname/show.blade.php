@extends('layouts.app')
@section('title', 'Detail Opname')
@section('content')
@php $statusBadge = ['draft' => 'secondary', 'diajukan' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger']; @endphp
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('opname.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold font-mono">{{ $sesi->nomor }}</h1>
    <x-badge :variant="$statusBadge[$sesi->status]" :value="ucfirst($sesi->status)" />
</div>

<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Info Opname</h3>
        <div class="flex justify-between"><span class="text-gray-500">Dibuat oleh</span><span>{{ $sesi->dibuatOleh?->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ $sesi->tanggal_mulai->format('d M Y H:i') }}</span></div>
        @if($sesi->disetujuiOleh)
        <div class="flex justify-between"><span class="text-gray-500">Disetujui oleh</span><span>{{ $sesi->disetujuiOleh->name }}</span></div>
        @endif
        @if($sesi->catatan)
        <div class="pt-2 border-t"><p class="text-gray-500 text-xs">{{ $sesi->catatan }}</p></div>
        @endif
        @if($sesi->catatan_penolakan)
        <div class="pt-2 border-t bg-red-50 rounded p-2">
            <p class="text-xs text-red-600"><strong>Alasan Penolakan:</strong> {{ $sesi->catatan_penolakan }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Action Buttons --}}
<div class="flex gap-3 mb-4">
    @if($sesi->status === 'draft')
    <form method="POST" action="{{ route('opname.ajukan', $sesi) }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">📤 Ajukan untuk Approval</button>
    </form>
    @endif

    @can('opname.approve')
    @if($sesi->status === 'diajukan')
    <form method="POST" action="{{ route('opname.approve', $sesi) }}" class="flex gap-2">
        @csrf
        <input type="hidden" name="action" value="setujui">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700"
                onclick="return confirm('Setujui opname? Stok akan dikoreksi.')">✓ Setujui</button>
    </form>
    <button onclick="document.getElementById('modal-tolak').classList.remove('hidden')"
            class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm rounded-lg hover:bg-red-100">✗ Tolak</button>
    @endif
    @endcan
</div>

{{-- Detail Table --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold">Detail Selisih</h2></div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Barang</th>
                <th class="px-4 py-2 text-left">Lokasi</th>
                <th class="px-4 py-2 text-right">Stok Sistem</th>
                <th class="px-4 py-2 text-right">Stok Fisik</th>
                <th class="px-4 py-2 text-right">Selisih</th>
                <th class="px-4 py-2 text-right">Nilai Selisih</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($sesi->detail as $d)
            <tr class="hover:bg-gray-50 {{ $d->selisih != 0 ? 'bg-yellow-50' : '' }}">
                <td class="px-4 py-2">
                    <p class="font-medium">{{ $d->barang?->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $d->barang?->kode }}</p>
                </td>
                <td class="px-4 py-2 text-gray-500 text-xs">{{ $d->lokasiRak?->kode ?? '-' }}</td>
                <td class="px-4 py-2 text-right">{{ $d->stok_sistem }}</td>
                <td class="px-4 py-2 text-right">{{ $d->stok_fisik }}</td>
                <td class="px-4 py-2 text-right font-medium {{ $d->selisih > 0 ? 'text-green-600' : ($d->selisih < 0 ? 'text-red-600' : 'text-gray-400') }}">
                    {{ $d->selisih > 0 ? '+' : '' }}{{ $d->selisih }}
                </td>
                <td class="px-4 py-2 text-right text-xs {{ $d->nilai_selisih != 0 ? 'font-medium' : 'text-gray-400' }}">
                    {{ $d->nilai_selisih != 0 ? 'Rp ' . number_format(abs($d->nilai_selisih), 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Tolak --}}
<div id="modal-tolak" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-red-600">Tolak Opname</h3>
            <button onclick="document.getElementById('modal-tolak').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('opname.approve', $sesi) }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="action" value="tolak">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan *</label>
                <textarea name="catatan_penolakan" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Tolak Opname</button>
                <button type="button" onclick="document.getElementById('modal-tolak').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
