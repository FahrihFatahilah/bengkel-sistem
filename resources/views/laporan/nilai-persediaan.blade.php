@extends('layouts.app')
@section('title', 'Nilai Persediaan')
@section('content')
<h1 class="text-xl font-semibold mb-6">💰 Laporan Nilai Persediaan</h1>
<div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-sm">
    Total Nilai Persediaan: <strong>Rp {{ number_format($total_nilai, 0, ',', '.') }}</strong>
</div>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-right">Stok</th>
                <th class="px-4 py-3 text-right">Harga Beli</th>
                <th class="px-4 py-3 text-right">Nilai Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang as $b)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-mono text-xs">{{ $b['kode'] }}</td>
                <td class="px-4 py-2 font-medium">{{ $b['nama'] }}</td>
                <td class="px-4 py-2 text-gray-500">{{ $b['kategori'] ?? '-' }}</td>
                <td class="px-4 py-2 text-right">{{ $b['stok'] }}</td>
                <td class="px-4 py-2 text-right">Rp {{ number_format($b['harga_beli'], 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($b['nilai_total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
