@extends('layouts.app')
@section('title', $supplier->nama)
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('supplier.index') }}" class="p-1.5 rounded hover:bg-gray-100">←</a>
    <h1 class="text-xl font-semibold">{{ $supplier->nama }}</h1>
    <x-badge :variant="$supplier->is_active ? 'default' : 'secondary'" :value="$supplier->is_active ? 'Aktif' : 'Nonaktif'" />
</div>
<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Info Supplier</h3>
        <div class="flex justify-between"><span class="text-gray-500">Kontak</span><span>{{ $supplier->kontak ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Email</span><span>{{ $supplier->email ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Alamat</span><span>{{ $supplier->alamat ?? '-' }}</span></div>
    </div>
</div>
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold">Riwayat Pembelian</h2></div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Nomor</th>
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-left">Ref PO</th>
                <th class="px-4 py-2 text-right">Total</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pembelian as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-mono text-xs">{{ $p->nomor }}</td>
                <td class="px-4 py-2">{{ $p->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-2 text-gray-500">{{ $p->referensi_po ?? '-' }}</td>
                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('pembelian.show', $p) }}" class="text-xs text-blue-600 hover:underline">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat pembelian</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $pembelian->links() }}</div>
</div>
@endsection
