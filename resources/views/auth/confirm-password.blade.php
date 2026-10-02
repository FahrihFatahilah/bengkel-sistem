@extends('layouts.auth')
@section('title', 'Konfirmasi Password')
@section('content')
<div class="w-full max-w-sm">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <h1 class="text-lg font-bold mb-2">Konfirmasi Password</h1>
        <p class="text-sm text-gray-500 mb-6">Masukkan password Anda untuk melanjutkan.</p>
        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Konfirmasi</button>
        </form>
    </div>
</div>
@endsection
