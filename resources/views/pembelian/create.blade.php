@extends('layouts.app')
@section('title', 'Tambah Pembelian')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('pembelian.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">Tambah Pembelian</h1>
</div>

<div class="bg-white rounded-lg border border-gray-200 max-w-3xl"
     x-data="{
        items: [{ barang_id: '', qty: 1, harga_beli: 0 }],
        barangList: {{ $barang->toJson() }},
        addItem() { this.items.push({ barang_id: '', qty: 1, harga_beli: 0 }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        pilihBarang(i, id) {
            const b = this.barangList.find(x => x.id === id);
            if (b) this.items[i].harga_beli = b.harga_beli;
        },
        get total() { return this.items.reduce((s, i) => s + (parseFloat(i.harga_beli)||0) * (parseInt(i.qty)||0), 0); }
     }">
    <div class="px-6 py-4 border-b border-gray-100"><h2 class="text-sm font-semibold">Form Pembelian</h2></div>
    <form method="POST" action="{{ route('pembelian.store') }}" class="p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                <select name="supplier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($supplier as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Referensi PO / Nota</label>
            <input type="text" name="referensi_po" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- Items --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700">Item Barang *</label>
                <button type="button" @click="addItem()" class="text-xs text-blue-600 hover:underline">+ Tambah Baris</button>
            </div>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Barang</th>
                            <th class="px-3 py-2 text-right w-24">Qty</th>
                            <th class="px-3 py-2 text-right w-36">Harga Beli</th>
                            <th class="px-3 py-2 text-right w-36">Subtotal</th>
                            <th class="px-3 py-2 w-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="i">
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2">
                                    <select :name="`items[${i}][barang_id]`" @change="pilihBarang(i, $event.target.value)" x-model="item.barang_id" required
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($barang as $b)
                                        <option value="{{ $b->id }}">{{ $b->kode }} — {{ $b->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="`items[${i}][qty]`" x-model="item.qty" min="1" required
                                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs text-right">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="`items[${i}][harga_beli]`" x-model="item.harga_beli" min="0" required
                                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs text-right">
                                </td>
                                <td class="px-3 py-2 text-right text-xs font-medium"
                                    x-text="'Rp ' + ((parseFloat(item.harga_beli)||0) * (parseInt(item.qty)||0)).toLocaleString('id-ID')"></td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="removeItem(i)" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="border-t border-gray-200 bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-3 py-2 text-right text-sm font-medium">Total</td>
                            <td class="px-3 py-2 text-right font-bold text-sm" x-text="'Rp ' + total.toLocaleString('id-ID')"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan Pembelian</button>
            <a href="{{ route('pembelian.index') }}" class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
