@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">User Management</h1>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">+ Tambah User</button>
</div>
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama / email..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <button type="submit" class="px-3 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Cari</button>
</form>
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Role</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $u)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $u->email }}</td>
                <td class="px-4 py-3 text-center">
                    <x-badge variant="default" :value="$u->roles->first()?->name ?? '-'" />
                </td>
                <td class="px-4 py-3 text-center">
                    <x-badge :variant="$u->is_active ? 'success' : 'secondary'" :value="$u->is_active ? 'Aktif' : 'Nonaktif'" />
                </td>
                <td class="px-4 py-3">
                    <button onclick="editUser('{{ $u->id }}', '{{ $u->name }}', '{{ $u->email }}', '{{ $u->roles->first()?->name }}', {{ $u->is_active ? 'true' : 'false' }})"
                            class="px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded">Edit</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada user</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->withQueryString()->links() }}</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Tambah User</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-3">
            @csrf
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Edit User</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="form-edit" method="POST" class="p-6 space-y-3">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" id="edit-name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="edit-email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" minlength="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select name="role" id="edit-role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="edit-active" class="rounded border-gray-300">
                <label for="edit-active" class="text-sm text-gray-700">User aktif</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-sm rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, name, email, role, isActive) {
    document.getElementById('form-edit').action = `/users/${id}`;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role;
    document.getElementById('edit-active').checked = isActive;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
