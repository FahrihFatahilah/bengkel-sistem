@extends('layouts.app')
@section('title', 'Buat Work Order')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('work-order.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">Buat Work Order</h1>
</div>

<div class="max-w-xl"
     x-data="{
        nopol: '',
        loading: false,
        found: null,
        kendaraanId: '',
        pelangganId: '',
        namaPelanggan: '',
        noHp: '',
        merek: '',
        tipe: '',
        tahun: '',
        async cariNopol() {
            const q = this.nopol.trim().toUpperCase();
            if (q.length < 3) { this.found = null; return; }
            this.loading = true;
            try {
                const res = await fetch(`/api/kendaraan/cari?nopol=${encodeURIComponent(q)}`);
                const data = await res.json();
                if (data.found) {
                    this.found = true;
                    this.kendaraanId = data.kendaraan_id;
                    this.pelangganId = data.pelanggan_id;
                    this.namaPelanggan = data.nama;
                    this.noHp = data.no_hp;
                    this.merek = data.merek;
                    this.tipe = data.tipe;
                    this.tahun = data.tahun;
                } else {
                    this.found = false;
                    this.kendaraanId = '';
                    this.pelangganId = '';
                    this.namaPelanggan = '';
                    this.noHp = '';
                    this.merek = '';
                    this.tipe = '';
                    this.tahun = '';
                }
            } finally {
                this.loading = false;
            }
        }
     }">

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Informasi Kendaraan & Pelanggan</h2>
        </div>

        <form method="POST" action="{{ route('work-order.store') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="kendaraan_id" :value="kendaraanId">
            <input type="hidden" name="pelanggan_id" :value="pelangganId">

            {{-- Nomor Polisi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Polisi <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="text" name="nomor_polisi"
                           x-model="nopol"
                           @input.debounce.500ms="cariNopol()"
                           @keydown.enter.prevent="cariNopol()"
                           placeholder="Contoh: B 1234 ABC"
                           required
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nomor_polisi') border-red-400 @enderror"
                           value="{{ old('nomor_polisi') }}">
                    <button type="button" @click="cariNopol()"
                            class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm hover:bg-gray-200">
                        <span x-show="!loading">🔍</span>
                        <span x-show="loading">⏳</span>
                    </button>
                </div>
                @error('nomor_polisi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                {{-- Status pencarian --}}
                <div x-show="found === true" class="mt-2 flex items-center gap-2 text-xs text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    ✓ Kendaraan ditemukan — data pelanggan diisi otomatis
                </div>
                <div x-show="found === false" class="mt-2 flex items-center gap-2 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                    ℹ Kendaraan baru — isi data pelanggan & kendaraan di bawah
                </div>
            </div>

            {{-- Info kendaraan (muncul kalau baru) --}}
            <div x-show="found === false" class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Merek</label>
                    <input type="text" name="merek" x-model="merek" placeholder="Honda"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                    <input type="text" name="tipe" x-model="tipe" placeholder="Beat"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <input type="number" name="tahun" x-model="tahun" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Info kendaraan (readonly kalau sudah ada) --}}
            <div x-show="found === true" class="bg-gray-50 rounded-lg px-4 py-3 text-sm text-gray-600 grid grid-cols-3 gap-2">
                <div><span class="text-xs text-gray-400 block">Merek</span><span x-text="merek || '-'"></span></div>
                <div><span class="text-xs text-gray-400 block">Tipe</span><span x-text="tipe || '-'"></span></div>
                <div><span class="text-xs text-gray-400 block">Tahun</span><span x-text="tahun || '-'"></span></div>
            </div>

            <hr class="border-gray-100">

            {{-- Nama Pelanggan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pelanggan"
                       x-model="namaPelanggan"
                       :readonly="found === true"
                       :class="found === true ? 'bg-gray-50 text-gray-500' : ''"
                       placeholder="Nama lengkap pelanggan"
                       required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pelanggan') border-red-400 @enderror"
                       value="{{ old('nama_pelanggan') }}">
                @error('nama_pelanggan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- No HP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp"
                       x-model="noHp"
                       :readonly="found === true"
                       :class="found === true ? 'bg-gray-50 text-gray-500' : ''"
                       placeholder="08xxxxxxxxxx"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('no_hp') }}">
            </div>

            <hr class="border-gray-100">

            {{-- Keluhan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan <span class="text-red-500">*</span></label>
                <textarea name="keluhan" rows="3" required
                          placeholder="Deskripsikan keluhan kendaraan secara detail..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('keluhan') border-red-400 @enderror">{{ old('keluhan') }}</textarea>
                @error('keluhan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Buat Work Order →
                </button>
                <a href="{{ route('work-order.index') }}"
                   class="px-4 py-2.5 bg-gray-100 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
