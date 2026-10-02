@extends('layouts.app')
@section('title', 'WO ' . $workOrder->nomor)
@section('content')
@php
$statusBadge = ['estimasi' => 'secondary', 'proses' => 'default', 'menunggu_pembayaran' => 'warning', 'lunas' => 'success', 'dibatalkan' => 'danger'];
$statusLabel = ['estimasi' => 'Estimasi', 'proses' => 'Proses', 'menunggu_pembayaran' => 'Menunggu Bayar', 'lunas' => 'Lunas', 'dibatalkan' => 'Dibatalkan'];
$locked = $workOrder->status === 'lunas' || $workOrder->status === 'dibatalkan';
@endphp

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('work-order.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold font-mono">{{ $workOrder->nomor }}</h1>
    <x-badge :variant="$statusBadge[$workOrder->status]" :value="$statusLabel[$workOrder->status]" />
    @if($workOrder->status === 'lunas')
    <a href="{{ route('work-order.show', $workOrder) }}?print=1" target="_blank"
       class="ml-auto px-3 py-1.5 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">🖨️ Cetak Invoice</a>
    @endif
</div>

<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Kendaraan & Pelanggan</h3>
        <div class="flex justify-between"><span class="text-gray-500">Nopol</span><span class="font-bold">{{ $workOrder->kendaraan?->nomor_polisi }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kendaraan</span><span>{{ $workOrder->kendaraan?->merek }} {{ $workOrder->kendaraan?->tipe }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Pelanggan</span><span>{{ $workOrder->pelanggan?->nama }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">HP</span><span>{{ $workOrder->pelanggan?->no_hp ?? '-' }}</span></div>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Info WO</h3>
        <div class="flex justify-between"><span class="text-gray-500">Mekanik</span><span>{{ $workOrder->mekanik?->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kasir</span><span>{{ $workOrder->kasir?->name ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Masuk</span><span>{{ $workOrder->tanggal_masuk->format('d M Y H:i') }}</span></div>
        @if($workOrder->tanggal_selesai)
        <div class="flex justify-between"><span class="text-gray-500">Selesai</span><span>{{ $workOrder->tanggal_selesai->format('d M Y H:i') }}</span></div>
        @endif
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Keluhan</h3>
        <p class="text-gray-700">{{ $workOrder->keluhan }}</p>
        @if($workOrder->diagnosa)
        <p class="text-xs text-gray-500 mt-2"><strong>Diagnosa:</strong> {{ $workOrder->diagnosa }}</p>
        @endif
    </div>
</div>

{{-- Item List --}}
<div class="bg-white rounded-lg border border-gray-200 mb-4">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold">Item Pekerjaan</h2>
        @if(!$locked && in_array($workOrder->status, ['proses', 'estimasi']))
        <button onclick="document.getElementById('modal-tambah-item').classList.remove('hidden')"
                class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah Item</button>
        @endif
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Item</th>
                <th class="px-4 py-2 text-right">Harga Barang</th>
                <th class="px-4 py-2 text-right">Tarif Pasang</th>
                <th class="px-4 py-2 text-right">Qty</th>
                <th class="px-4 py-2 text-right">Subtotal</th>
                <th class="px-4 py-2 text-center">Serah Terima</th>
                @if(!$locked) <th class="px-4 py-2"></th> @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($workOrder->detail as $d)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $d->nama_item }}</p>
                    @if($d->barang) <p class="text-xs text-gray-400">{{ $d->barang->kode }}</p> @endif
                </td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($d->harga_barang, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($d->tarif_pasang, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">{{ $d->qty }}</td>
                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    @if($d->status_serah_terima === 'sudah')
                        <span class="text-green-600 text-xs">✓ Diserahkan</span>
                    @elseif($workOrder->status === 'menunggu_pembayaran')
                        <form method="POST" action="{{ route('work-order.serah-terima', [$workOrder, $d]) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Serahkan</button>
                        </form>
                    @else
                        <span class="text-gray-400 text-xs">Belum</span>
                    @endif
                </td>
                @if(!$locked)
                <td class="px-4 py-3 text-right">
                    @if(!$d->is_locked && in_array($workOrder->status, ['proses', 'estimasi', 'menunggu_pembayaran']))
                    <button onclick="openEditItem('{{ $d->id }}', {{ $d->harga_barang }}, {{ $d->tarif_pasang }}, {{ $d->qty }})"
                            class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Edit</button>
                    <form method="POST" action="{{ route('work-order.item.destroy', [$workOrder, $d]) }}" class="inline"
                          onsubmit="return confirm('Hapus item ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded">Hapus</button>
                    </form>
                    @endif
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada item</td></tr>
            @endforelse
        </tbody>
        <tfoot class="border-t border-gray-200 bg-gray-50">
            <tr>
                <td colspan="4" class="px-4 py-3 text-right text-sm font-medium">Total</td>
                <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($workOrder->total, 0, ',', '.') }}</td>
                <td colspan="{{ $locked ? 1 : 2 }}"></td>
            </tr>
            @if($workOrder->diskon > 0)
            <tr>
                <td colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">Diskon</td>
                <td class="px-4 py-2 text-right text-red-500">- Rp {{ number_format($workOrder->diskon, 0, ',', '.') }}</td>
                <td colspan="{{ $locked ? 1 : 2 }}"></td>
            </tr>
            <tr>
                <td colspan="4" class="px-4 py-2 text-right text-sm font-bold">Total Bayar</td>
                <td class="px-4 py-2 text-right font-bold text-blue-600">Rp {{ number_format($workOrder->total_bayar, 0, ',', '.') }}</td>
                <td colspan="{{ $locked ? 1 : 2 }}"></td>
            </tr>
            @endif
        </tfoot>
    </table>
</div>

{{-- Action Buttons --}}
<div class="flex gap-3 flex-wrap">
    @if($workOrder->status === 'estimasi')
    <form method="POST" action="{{ route('work-order.konfirmasi-estimasi', $workOrder) }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
                onclick="return confirm('Konfirmasi estimasi? Stok akan dikurangi.')">✓ Konfirmasi Estimasi</button>
    </form>
    @endif

    @if($workOrder->status === 'proses')
    <form method="POST" action="{{ route('work-order.menunggu-pembayaran', $workOrder) }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
            💳 Kirim ke Kasir
        </button>
    </form>
    @endif

    @can('wo.bayar')
    @if($workOrder->status === 'menunggu_pembayaran')
    <button onclick="document.getElementById('modal-bayar').classList.remove('hidden')"
            class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">💰 Proses Pembayaran</button>
    @endif
    @endcan

    @if(!$locked)
    <button onclick="document.getElementById('modal-batalkan').classList.remove('hidden')"
            class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm rounded-lg hover:bg-red-100">Batalkan WO</button>
    @endif
</div>

{{-- Modal Tambah Item --}}
<div id="modal-tambah-item" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-xl"
         x-data="{
            kategoriId: '',
            barangId: '',
            hargaBarang: 0,
            tarifPasang: 0,
            qty: 1,
            namaItem: '',
            kategoriList: {{ $kategori->toJson() }},
            tarifList: {{ $tarifJasa->toJson() }},
            get barangList() {
                if (!this.kategoriId) return [];
                const k = this.kategoriList.find(x => x.id === this.kategoriId);
                return k ? k.barang : [];
            },
            pilihKategori(id) {
                this.kategoriId = id;
                this.barangId = '';
                this.hargaBarang = 0;
                this.namaItem = '';
            },
            pilihBarang(id) {
                this.barangId = id;
                const b = this.barangList.find(x => x.id === id);
                if (b) { this.hargaBarang = b.harga_jual; this.namaItem = b.nama; }
            },
            pilihTarif(id) {
                const t = this.tarifList.find(x => x.id === id);
                if (t) this.tarifPasang = t.tarif;
            },
            reset() {
                this.kategoriId = ''; this.barangId = '';
                this.hargaBarang = 0; this.tarifPasang = 0;
                this.qty = 1; this.namaItem = '';
            },
            get subtotal() { return (parseFloat(this.hargaBarang||0) + parseFloat(this.tarifPasang||0)) * parseInt(this.qty||1); }
         }">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah Item</h3>
            <button @click="reset(); document.getElementById('modal-tambah-item').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
        </div>
        <form method="POST" action="{{ route('work-order.item.store', $workOrder) }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="barang_id" :value="barangId">

            {{-- Step 1: Pilih Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Barang</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($kategori as $kat)
                    <button type="button"
                            @click="pilihKategori('{{ $kat->id }}')"
                            :class="kategoriId === '{{ $kat->id }}' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                            class="flex items-center gap-2 px-3 py-2 border rounded-lg text-sm text-left transition-colors">
                        <span class="text-base">📦</span>
                        <span class="truncate">{{ $kat->nama }}</span>
                    </button>
                    @endforeach
                    <button type="button"
                            @click="pilihKategori('jasa')"
                            :class="kategoriId === 'jasa' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                            class="flex items-center gap-2 px-3 py-2 border rounded-lg text-sm text-left transition-colors">
                        <span class="text-base">🔧</span>
                        <span>Jasa Saja</span>
                    </button>
                </div>
            </div>

            {{-- Step 2: Pilih Barang (muncul kalau bukan jasa) --}}
            <div x-show="kategoriId && kategoriId !== 'jasa'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                <select @change="pilihBarang($event.target.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih barang --</option>
                    <template x-for="b in barangList" :key="b.id">
                        <option :value="b.id" x-text="`${b.kode} — ${b.nama} (stok: ${b.stok})`"></option>
                    </template>
                </select>
                <p x-show="barangList.length === 0" class="text-xs text-gray-400 mt-1">Tidak ada barang di kategori ini.</p>
            </div>

            {{-- Nama Item --}}
            <div x-show="kategoriId">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item <span class="text-red-500">*</span></label>
                <input type="text" name="nama_item" x-model="namaItem" required
                       placeholder="Nama pekerjaan / barang"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tarif Jasa --}}
            <div x-show="kategoriId">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif Jasa (opsional)</label>
                <select name="tarif_jasa_id" @change="pilihTarif($event.target.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tanpa tarif jasa --</option>
                    @foreach($tarifJasa as $t)
                    <option value="{{ $t->id }}">{{ $t->nama }} — Rp {{ number_format($t->tarif, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Harga & Qty --}}
            <div x-show="kategoriId" class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Harga Barang</label>
                    <input type="number" name="harga_barang" x-model="hargaBarang" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tarif Pasang</label>
                    <input type="number" name="tarif_pasang" x-model="tarifPasang" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                    <input type="number" name="qty" x-model="qty" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Subtotal preview --}}
            <div x-show="kategoriId" class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-2.5 text-sm flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <strong class="text-blue-700">Rp <span x-text="subtotal.toLocaleString('id-ID')"></span></strong>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" x-show="kategoriId"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Tambah Item</button>
                <button type="button" @click="reset(); document.getElementById('modal-tambah-item').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Bayar --}}
<div id="modal-bayar" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl"
         x-data="{ diskon: 0, total: {{ $workOrder->total }}, get totalBayar() { return this.total - parseFloat(this.diskon || 0); } }">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Proses Pembayaran</h3>
            <button onclick="document.getElementById('modal-bayar').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('work-order.bayar', $workOrder) }}" class="p-6 space-y-4">
            @csrf
            <div class="bg-gray-50 rounded-lg p-4 text-sm space-y-1">
                <div class="flex justify-between"><span>Total</span><span class="font-medium">Rp {{ number_format($workOrder->total, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-red-500">
                    <span>Diskon</span>
                    <span>- Rp <span x-text="parseFloat(diskon || 0).toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between font-bold text-blue-600 border-t pt-1">
                    <span>Total Bayar</span>
                    <span>Rp <span x-text="totalBayar.toLocaleString('id-ID')"></span></span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diskon</label>
                <input type="number" name="diskon" x-model="diskon" min="0" value="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['tunai' => '💵 Tunai', 'transfer' => '🏦 Transfer', 'qris' => '📱 QRIS'] as $val => $label)
                    <label class="flex items-center justify-center gap-1.5 border rounded-lg px-3 py-2 text-sm cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="metode_bayar" value="{{ $val }}" class="sr-only"> {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-medium">✓ Konfirmasi Bayar</button>
                <button type="button" onclick="document.getElementById('modal-bayar').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Batalkan --}}
<div id="modal-batalkan" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-red-600">Batalkan Work Order</h3>
            <button onclick="document.getElementById('modal-batalkan').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('work-order.batalkan', $workOrder) }}" class="p-6 space-y-4">
            @csrf
            <p class="text-sm text-gray-600">Stok barang yang sudah dipakai akan dikembalikan otomatis.</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan *</label>
                <textarea name="catatan" rows="3" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Batalkan WO</button>
                <button type="button" onclick="document.getElementById('modal-batalkan').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Kembali</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Item --}}
<div id="modal-edit-item" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-sm shadow-xl"
         x-data="{ hargaBarang: 0, tarifPasang: 0, qty: 1, detailId: '',
                   get subtotal() { return (parseFloat(this.hargaBarang||0) + parseFloat(this.tarifPasang||0)) * parseInt(this.qty||1); } }"
         x-on:open-edit.window="hargaBarang = $event.detail.harga; tarifPasang = $event.detail.tarif; qty = $event.detail.qty; detailId = $event.detail.id">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Edit Item</h3>
            <button @click="document.getElementById('modal-edit-item').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
        </div>
        <form method="POST" :action="`/work-order/{{ $workOrder->id }}/item/${detailId}`" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Barang</label>
                <input type="number" name="harga_barang" x-model="hargaBarang" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tarif Pasang</label>
                <input type="number" name="tarif_pasang" x-model="tarifPasang" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                <input type="number" name="qty" x-model="qty" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-2.5 text-sm flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <strong class="text-blue-700">Rp <span x-text="subtotal.toLocaleString('id-ID')"></span></strong>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" @click="document.getElementById('modal-edit-item').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditItem(id, harga, tarif, qty) {
    document.getElementById('modal-edit-item').classList.remove('hidden');
    window.dispatchEvent(new CustomEvent('open-edit', { detail: { id, harga, tarif, qty } }));
}
</script>
@endsection
