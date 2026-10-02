<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang
    Route::middleware('permission:barang.view')->group(function () {
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    });
    Route::middleware('permission:barang.create')->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    });
    Route::middleware('permission:barang.view')->group(function () {
        Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    });
    Route::middleware('permission:barang.edit')->group(function () {
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    });

    // Stok
    Route::middleware('permission:stok.view')->group(function () {
        Route::get('/barang/{barang}/kartu-stok', [StokController::class, 'kartuStok'])->name('stok.kartu');
    });
    Route::middleware('permission:stok.mutasi')->group(function () {
        Route::get('/stok/mutasi', [StokController::class, 'indexMutasi'])->name('stok.mutasi');
        Route::post('/stok/mutasi', [StokController::class, 'mutasi'])->name('stok.mutasi.store');
    });

    // Pembelian
    Route::middleware('permission:pembelian.view')->group(function () {
        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
    });
    Route::middleware('permission:pembelian.create')->group(function () {
        Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
    });
    Route::middleware('permission:pembelian.view')->group(function () {
        Route::get('/pembelian/{pembelian}', [PembelianController::class, 'show'])->name('pembelian.show');
    });

    // Work Order
    Route::get('/api/kendaraan/cari', [WorkOrderController::class, 'cariKendaraan']);
    Route::middleware('permission:wo.view')->group(function () {
        Route::get('/work-order', [WorkOrderController::class, 'index'])->name('work-order.index');
    });
    Route::middleware('permission:wo.create')->group(function () {
        Route::get('/work-order/create', [WorkOrderController::class, 'create'])->name('work-order.create');
        Route::post('/work-order', [WorkOrderController::class, 'store'])->name('work-order.store');
        Route::post('/work-order/{workOrder}/item', [WorkOrderController::class, 'tambahItem'])->name('work-order.item.store');
        Route::post('/work-order/{workOrder}/konfirmasi-estimasi', [WorkOrderController::class, 'konfirmasiEstimasi'])->name('work-order.konfirmasi-estimasi');
        Route::post('/work-order/{workOrder}/menunggu-pembayaran', [WorkOrderController::class, 'menungguPembayaran'])->name('work-order.menunggu-pembayaran');
    });
    Route::middleware('permission:wo.view')->group(function () {
        Route::get('/work-order/{workOrder}', [WorkOrderController::class, 'show'])->name('work-order.show');
    });
    Route::middleware('permission:wo.edit')->group(function () {
        Route::put('/work-order/{workOrder}/item/{detail}', [WorkOrderController::class, 'updateItem'])->name('work-order.item.update');
        Route::delete('/work-order/{workOrder}/item/{detail}', [WorkOrderController::class, 'hapusItem'])->name('work-order.item.destroy');
        Route::post('/work-order/{workOrder}/item/{detail}/serah-terima', [WorkOrderController::class, 'serahTerima'])->name('work-order.serah-terima');
        Route::post('/work-order/{workOrder}/batalkan', [WorkOrderController::class, 'batalkan'])->name('work-order.batalkan');
    });
    Route::middleware('permission:wo.bayar')->group(function () {
        Route::post('/work-order/{workOrder}/bayar', [WorkOrderController::class, 'bayar'])->name('work-order.bayar');
    });

    // Pelanggan
    Route::middleware('permission:pelanggan.view')->group(function () {
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
    });
    Route::middleware('permission:pelanggan.create')->group(function () {
        Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::post('/pelanggan/{pelanggan}/kendaraan', [PelangganController::class, 'storeKendaraan'])->name('pelanggan.kendaraan.store');
    });
    Route::middleware('permission:pelanggan.edit')->group(function () {
        Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
    });

    // Supplier
    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/{supplier}', [SupplierController::class, 'show'])->name('supplier.show');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');

    // Master Data
    Route::middleware('permission:master.view')->prefix('master')->name('master.')->group(function () {
        Route::get('/kategori', [MasterDataController::class, 'kategoriIndex'])->name('kategori');
        Route::post('/kategori', [MasterDataController::class, 'kategoriStore'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [MasterDataController::class, 'kategoriUpdate'])->name('kategori.update');

        Route::get('/rak', [MasterDataController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [MasterDataController::class, 'rakStore'])->name('rak.store');
        Route::put('/rak/{rak}', [MasterDataController::class, 'rakUpdate'])->name('rak.update');

        Route::get('/tarif-jasa', [MasterDataController::class, 'tarifIndex'])->name('tarif');
        Route::post('/tarif-jasa', [MasterDataController::class, 'tarifStore'])->name('tarif.store');
        Route::put('/tarif-jasa/{tarif}', [MasterDataController::class, 'tarifUpdate'])->name('tarif.update');

        Route::get('/paket-servis', [MasterDataController::class, 'paketIndex'])->name('paket');
        Route::post('/paket-servis', [MasterDataController::class, 'paketStore'])->name('paket.store');
    });

    // Opname
    Route::middleware('permission:opname.view')->group(function () {
        Route::get('/opname', [OpnameController::class, 'index'])->name('opname.index');
    });
    Route::middleware('permission:opname.create')->group(function () {
        Route::get('/opname/create', [OpnameController::class, 'create'])->name('opname.create');
        Route::post('/opname', [OpnameController::class, 'store'])->name('opname.store');
        Route::post('/opname/{opname}/ajukan', [OpnameController::class, 'ajukan'])->name('opname.ajukan');
    });
    Route::middleware('permission:opname.view')->group(function () {
        Route::get('/opname/{opname}', [OpnameController::class, 'show'])->name('opname.show');
    });
    Route::middleware('permission:opname.approve')->group(function () {
        Route::post('/opname/{opname}/approve', [OpnameController::class, 'approve'])->name('opname.approve');
    });

    // Laporan
    Route::middleware('permission:laporan.view')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/stok-minimum', [LaporanController::class, 'stokMinimum'])->name('stok-minimum');
        Route::get('/nilai-persediaan', [LaporanController::class, 'nilaiPersediaan'])->name('nilai-persediaan');
        Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('/pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
        Route::get('/opname', [LaporanController::class, 'opname'])->name('opname');
    });

    // User Management (admin only)
    Route::middleware('permission:user.manage')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
    });
});

require __DIR__ . '/auth.php';
