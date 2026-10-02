<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kontak')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lokasi_rak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->unique();
            $table->string('deskripsi')->nullable();
            $table->integer('kapasitas')->nullable();
            $table->boolean('is_frozen')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->uuid('kategori_id')->nullable();
            $table->string('satuan')->default('pcs');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->integer('stok')->default(0);
            $table->uuid('supplier_utama_id')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kategori_id')->references('id')->on('kategori_barang')->nullOnDelete();
            $table->foreign('supplier_utama_id')->references('id')->on('supplier')->nullOnDelete();
        });

        Schema::create('barang_lokasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('barang_id');
            $table->uuid('lokasi_rak_id');
            $table->integer('qty')->default(0);
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang')->cascadeOnDelete();
            $table->foreign('lokasi_rak_id')->references('id')->on('lokasi_rak')->cascadeOnDelete();
            $table->unique(['barang_id', 'lokasi_rak_id']);
        });

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kendaraan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelanggan_id');
            $table->string('nomor_polisi')->unique();
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->year('tahun')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->cascadeOnDelete();
        });

        Schema::create('master_tarif_jasa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('paket_servis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->decimal('harga_paket', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('paket_servis_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('paket_servis_id');
            $table->uuid('barang_id')->nullable();
            $table->uuid('tarif_jasa_id')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('paket_servis_id')->references('id')->on('paket_servis')->cascadeOnDelete();
            $table->foreign('barang_id')->references('id')->on('barang')->nullOnDelete();
            $table->foreign('tarif_jasa_id')->references('id')->on('master_tarif_jasa')->nullOnDelete();
        });

        Schema::create('pembelian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor')->unique();
            $table->uuid('supplier_id');
            $table->uuid('user_id');
            $table->string('referensi_po')->nullable();
            $table->date('tanggal');
            $table->decimal('total', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('supplier_id')->references('id')->on('supplier');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pembelian_id');
            $table->uuid('barang_id');
            $table->integer('qty');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('pembelian_id')->references('id')->on('pembelian')->cascadeOnDelete();
            $table->foreign('barang_id')->references('id')->on('barang');
        });

        Schema::create('work_order', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor')->unique();
            $table->uuid('kendaraan_id');
            $table->uuid('pelanggan_id');
            $table->uuid('mekanik_id');
            $table->uuid('kasir_id')->nullable();
            $table->text('keluhan');
            $table->text('diagnosa')->nullable();
            $table->enum('status', ['estimasi', 'proses', 'menunggu_pembayaran', 'lunas', 'dibatalkan'])->default('proses');
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->nullable();
            $table->uuid('approved_diskon_by')->nullable();
            $table->timestamp('tanggal_masuk')->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kendaraan_id')->references('id')->on('kendaraan');
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan');
            $table->foreign('mekanik_id')->references('id')->on('users');
            $table->foreign('kasir_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_diskon_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('work_order_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('work_order_id');
            $table->uuid('barang_id')->nullable();
            $table->uuid('tarif_jasa_id')->nullable();
            $table->string('nama_item');
            $table->decimal('harga_barang', 15, 2)->default(0);
            $table->decimal('tarif_pasang', 15, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->enum('status_serah_terima', ['belum', 'sudah'])->default('belum');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_order')->cascadeOnDelete();
            $table->foreign('barang_id')->references('id')->on('barang')->nullOnDelete();
            $table->foreign('tarif_jasa_id')->references('id')->on('master_tarif_jasa')->nullOnDelete();
        });

        Schema::create('stok_transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('barang_id');
            $table->uuid('lokasi_rak_id')->nullable();
            $table->uuid('user_id');
            $table->enum('tipe', ['masuk', 'keluar', 'mutasi_masuk', 'mutasi_keluar', 'koreksi', 'opname']);
            $table->integer('qty');
            $table->integer('saldo_sebelum');
            $table->integer('saldo_sesudah');
            $table->uuid('referensi_id')->nullable();
            $table->string('referensi_type')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('opname_sesi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor')->unique();
            $table->uuid('dibuat_oleh');
            $table->uuid('disetujui_oleh')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->timestamp('tanggal_mulai')->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dibuat_oleh')->references('id')->on('users');
            $table->foreign('disetujui_oleh')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('opname_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('opname_sesi_id');
            $table->uuid('barang_id');
            $table->uuid('lokasi_rak_id')->nullable();
            $table->integer('stok_sistem');
            $table->integer('stok_fisik')->nullable();
            $table->integer('selisih')->nullable();
            $table->decimal('nilai_selisih', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('opname_sesi_id')->references('id')->on('opname_sesi')->cascadeOnDelete();
            $table->foreign('barang_id')->references('id')->on('barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_detail');
        Schema::dropIfExists('opname_sesi');
        Schema::dropIfExists('stok_transaksi');
        Schema::dropIfExists('work_order_detail');
        Schema::dropIfExists('work_order');
        Schema::dropIfExists('pembelian_detail');
        Schema::dropIfExists('pembelian');
        Schema::dropIfExists('paket_servis_detail');
        Schema::dropIfExists('paket_servis');
        Schema::dropIfExists('master_tarif_jasa');
        Schema::dropIfExists('kendaraan');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('barang_lokasi');
        Schema::dropIfExists('barang');
        Schema::dropIfExists('lokasi_rak');
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('kategori_barang');
    }
};
