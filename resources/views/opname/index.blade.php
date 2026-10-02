@extends('layouts.app')
@section('title', 'Stock Opname')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Stock Opname</h1>
    @can('opname.create')
    <a href="{{ route('opname.create') }}" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Buat Opname</a>
    @endcan
</div>
@php $statusBadge = ['draft' => 'secondary', 'diajukan' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger']; @endphp
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nomor</th>
                <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sesi as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-medium">{{ $s->nomor }}</td>
                <td class="px-4 py-3">{{ $s->dibuatOleh?->name }}</td>
                <td class="px-4 py-3 text-center">
                    <x-badge :variant="$statusBadge[$s->status] ?? 'secondary'" :value="ucfirst($s->status)" />
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $s->tanggal_mulai->format('d M Y H:i') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('opname.show', $s) }}" class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada sesi opname</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sesi->links() }}</div>
@endsection
