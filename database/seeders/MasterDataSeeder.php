<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LokasiRak;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Oli & Pelumas'],
            ['nama' => 'Filter'],
            ['nama' => 'Busi'],
            ['nama' => 'Rem'],
            ['nama' => 'Kelistrikan'],
            ['nama' => 'Rantai & Gear'],
            ['nama' => 'Ban & Velg'],
            ['nama' => 'Body & Aksesoris'],
        ];

        foreach ($kategori as $k) {
            KategoriBarang::firstOrCreate(['nama' => $k['nama']], $k);
        }

        $supplier = [
            ['nama' => 'PT Astra Honda Motor',   'kontak' => '021-12345678', 'email' => 'order@ahm.co.id'],
            ['nama' => 'CV Sumber Jaya Parts',   'kontak' => '0812-3456789', 'email' => 'sales@sumberjaya.com'],
            ['nama' => 'UD Maju Bersama',         'kontak' => '0856-7890123', 'email' => null],
        ];

        foreach ($supplier as $s) {
            Supplier::firstOrCreate(['nama' => $s['nama']], $s);
        }

        $rak = [
            ['kode' => 'RAK-A1', 'deskripsi' => 'Rak A Baris 1 - Oli & Filter'],
            ['kode' => 'RAK-A2', 'deskripsi' => 'Rak A Baris 2 - Busi & Kelistrikan'],
            ['kode' => 'RAK-B1', 'deskripsi' => 'Rak B Baris 1 - Rem & Kampas'],
            ['kode' => 'RAK-B2', 'deskripsi' => 'Rak B Baris 2 - Rantai & Gear'],
            ['kode' => 'RAK-C1', 'deskripsi' => 'Rak C - Ban & Body'],
        ];

        foreach ($rak as $r) {
            LokasiRak::firstOrCreate(['kode' => $r['kode']], $r);
        }

        $oliKategori = KategoriBarang::where('nama', 'Oli & Pelumas')->first();
        $busiKategori = KategoriBarang::where('nama', 'Busi')->first();
        $filterKategori = KategoriBarang::where('nama', 'Filter')->first();
        $sup = Supplier::first();

        $barang = [
            ['kode' => 'OLI-001', 'nama' => 'Oli Mesin AHM 0.8L',     'kategori_id' => $oliKategori?->id,    'satuan' => 'botol', 'harga_beli' => 28000,  'harga_jual' => 35000,  'stok_minimum' => 10, 'stok' => 50],
            ['kode' => 'OLI-002', 'nama' => 'Oli Mesin Yamalube 1L',   'kategori_id' => $oliKategori?->id,    'satuan' => 'botol', 'harga_beli' => 32000,  'harga_jual' => 42000,  'stok_minimum' => 10, 'stok' => 30],
            ['kode' => 'BSI-001', 'nama' => 'Busi NGK CR7HSA',         'kategori_id' => $busiKategori?->id,   'satuan' => 'pcs',   'harga_beli' => 15000,  'harga_jual' => 22000,  'stok_minimum' => 5,  'stok' => 20],
            ['kode' => 'BSI-002', 'nama' => 'Busi Denso U22FSR-U',     'kategori_id' => $busiKategori?->id,   'satuan' => 'pcs',   'harga_beli' => 18000,  'harga_jual' => 25000,  'stok_minimum' => 5,  'stok' => 3],
            ['kode' => 'FLT-001', 'nama' => 'Filter Oli Honda Beat',   'kategori_id' => $filterKategori?->id, 'satuan' => 'pcs',   'harga_beli' => 12000,  'harga_jual' => 18000,  'stok_minimum' => 5,  'stok' => 15],
            ['kode' => 'FLT-002', 'nama' => 'Filter Udara Vario 125',  'kategori_id' => $filterKategori?->id, 'satuan' => 'pcs',   'harga_beli' => 25000,  'harga_jual' => 35000,  'stok_minimum' => 3,  'stok' => 2],
        ];

        foreach ($barang as $b) {
            Barang::firstOrCreate(['kode' => $b['kode']], array_merge($b, ['supplier_utama_id' => $sup?->id]));
        }
    }
}
