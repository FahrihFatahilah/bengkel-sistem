<!DOCTYPE html>
<html lang="id" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    get isMobile() { return window.innerWidth < 1024 }
}" @resize.window="if (window.innerWidth >= 1024) sidebarOpen = true">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         style="display:none"></div>

    {{-- Sidebar --}}
    <aside x-show="sidebarOpen"
           x-bind:class="window.innerWidth < 1024
               ? 'fixed inset-y-0 left-0 z-30 w-64'
               : 'relative w-56 shrink-0'"
           class="flex flex-col bg-white border-r border-gray-200 transition-transform duration-200">

        <div class="flex items-center justify-between h-14 px-4 border-b border-gray-200 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="font-semibold text-sm">Bengkel MS</span>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto p-2 space-y-4 text-sm">
            {{-- Utama --}}
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Utama</p>
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>🏠</x-slot:icon> Dashboard
                </x-nav-link>

                {{-- Mekanik: buat & kelola WO --}}
                @can('wo.create')
                <x-nav-link href="{{ route('work-order.index') }}" :active="request()->routeIs('work-order.*')">
                    <x-slot:icon>🔧</x-slot:icon> Work Order
                </x-nav-link>
                @endcan

                {{-- Kasir: hanya lihat WO menunggu bayar --}}
                @cannot('wo.create')
                @can('wo.bayar')
                <x-nav-link href="{{ route('work-order.index') }}?status=menunggu_pembayaran" :active="request()->routeIs('work-order.*')">
                    <x-slot:icon>💳</x-slot:icon> Antrian Bayar
                </x-nav-link>
                @endcan
                @endcannot
            </div>

            {{-- Inventori — sembunyikan dari kasir --}}
            @cannot('wo.bayar')
            @canany(['barang.view', 'pembelian.view', 'stok.mutasi', 'opname.view'])
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Inventori</p>
                @can('barang.view')
                <x-nav-link href="{{ route('barang.index') }}" :active="request()->routeIs('barang.*')">
                    <x-slot:icon>📦</x-slot:icon> Barang
                </x-nav-link>
                @endcan
                @can('pembelian.view')
                <x-nav-link href="{{ route('pembelian.index') }}" :active="request()->routeIs('pembelian.*')">
                    <x-slot:icon>🛒</x-slot:icon> Pembelian
                </x-nav-link>
                @endcan
                @can('stok.mutasi')
                <x-nav-link href="{{ route('stok.mutasi') }}" :active="request()->routeIs('stok.mutasi')">
                    <x-slot:icon>🔄</x-slot:icon> Mutasi Stok
                </x-nav-link>
                @endcan
                @can('opname.view')
                <x-nav-link href="{{ route('opname.index') }}" :active="request()->routeIs('opname.*')">
                    <x-slot:icon>📋</x-slot:icon> Stock Opname
                </x-nav-link>
                @endcan
            </div>
            @endcanany
            @endcannot

            {{-- Data --}}
            @canany(['pelanggan.view'])
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Data</p>
                @can('pelanggan.view')
                <x-nav-link href="{{ route('pelanggan.index') }}" :active="request()->routeIs('pelanggan.*')">
                    <x-slot:icon>👥</x-slot:icon> Pelanggan
                </x-nav-link>
                @endcan
                @cannot('wo.bayar')
                <x-nav-link href="{{ route('supplier.index') }}" :active="request()->routeIs('supplier.*')">
                    <x-slot:icon>🚚</x-slot:icon> Supplier
                </x-nav-link>
                @endcannot
            </div>
            @endcanany

            @can('laporan.view')
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Laporan</p>
                <x-nav-link href="{{ route('laporan.penjualan') }}" :active="request()->routeIs('laporan.penjualan')">
                    <x-slot:icon>📊</x-slot:icon> Penjualan
                </x-nav-link>
                @cannot('wo.bayar')
                <x-nav-link href="{{ route('laporan.pembelian') }}" :active="request()->routeIs('laporan.pembelian')">
                    <x-slot:icon>📊</x-slot:icon> Pembelian
                </x-nav-link>
                <x-nav-link href="{{ route('laporan.stok-minimum') }}" :active="request()->routeIs('laporan.stok-minimum')">
                    <x-slot:icon>⚠️</x-slot:icon> Stok Minimum
                </x-nav-link>
                <x-nav-link href="{{ route('laporan.nilai-persediaan') }}" :active="request()->routeIs('laporan.nilai-persediaan')">
                    <x-slot:icon>💰</x-slot:icon> Nilai Persediaan
                </x-nav-link>
                @endcannot
            </div>
            @endcan

            @can('master.view')
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Master</p>
                <x-nav-link href="{{ route('master.kategori') }}" :active="request()->routeIs('master.kategori')">
                    <x-slot:icon>🏷️</x-slot:icon> Kategori
                </x-nav-link>
                <x-nav-link href="{{ route('master.rak') }}" :active="request()->routeIs('master.rak')">
                    <x-slot:icon>🗄️</x-slot:icon> Lokasi Rak
                </x-nav-link>
                <x-nav-link href="{{ route('master.tarif') }}" :active="request()->routeIs('master.tarif')">
                    <x-slot:icon>💵</x-slot:icon> Tarif Jasa
                </x-nav-link>
                <x-nav-link href="{{ route('master.paket') }}" :active="request()->routeIs('master.paket')">
                    <x-slot:icon>📁</x-slot:icon> Paket Servis
                </x-nav-link>
            </div>
            @endcan

            @can('user.manage')
            <div>
                <p class="px-3 py-1 text-xs font-medium text-gray-400 uppercase tracking-wider">Admin</p>
                <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                    <x-slot:icon>👤</x-slot:icon> Users
                </x-nav-link>
            </div>
            @endcan
        </nav>
    </aside>

    {{-- Main --}}
    <div class="flex flex-1 flex-col overflow-hidden min-w-0">

        <header class="flex items-center justify-between h-14 px-4 bg-white border-b border-gray-200 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="p-1.5 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <span class="font-medium text-sm truncate mx-2 lg:hidden">@yield('title', 'Dashboard')</span>

            <div class="flex items-center gap-2" x-data="{ open: false }">
                <span class="hidden sm:inline text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full capitalize">
                    {{ auth()->user()->getRoleNames()->first() ?? 'user' }}
                </span>
                <div class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm hover:bg-gray-100 px-2 py-1.5 rounded">
                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 mt-3 px-4 py-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 mt-3 px-4 py-2 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg flex items-center gap-2">
            <span>✗</span> {{ session('error') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mx-4 mt-3 px-4 py-2 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
