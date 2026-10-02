<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\StokTransaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function masuk(Barang $barang, int $qty, string $catatan = null, $referensi = null, string $lokasiRakId = null): StokTransaksi
    {
        return DB::transaction(function () use ($barang, $qty, $catatan, $referensi, $lokasiRakId) {
            $saldoSebelum = $barang->stok;
            $barang->increment('stok', $qty);

            if ($lokasiRakId) {
                $barang->lokasiRak()->updateOrCreate(
                    ['lokasi_rak_id' => $lokasiRakId],
                    ['qty' => DB::raw("qty + $qty")]
                );
            }

            return $this->catatTransaksi($barang, 'masuk', $qty, $saldoSebelum, $catatan, $referensi, $lokasiRakId);
        });
    }

    public function keluar(Barang $barang, int $qty, string $catatan = null, $referensi = null, string $lokasiRakId = null): StokTransaksi
    {
        return DB::transaction(function () use ($barang, $qty, $catatan, $referensi, $lokasiRakId) {
            if ($barang->stok < $qty) {
                throw new \Exception("Stok {$barang->nama} tidak mencukupi. Stok: {$barang->stok}, Dibutuhkan: {$qty}");
            }

            $saldoSebelum = $barang->stok;
            $barang->decrement('stok', $qty);

            if ($lokasiRakId) {
                $barang->lokasiRak()->where('lokasi_rak_id', $lokasiRakId)
                    ->update(['qty' => DB::raw("qty - $qty")]);
            }

            return $this->catatTransaksi($barang, 'keluar', $qty, $saldoSebelum, $catatan, $referensi, $lokasiRakId);
        });
    }

    public function mutasi(Barang $barang, int $qty, string $dariLokasiId, string $keLokasiId, string $catatan = null): void
    {
        DB::transaction(function () use ($barang, $qty, $dariLokasiId, $keLokasiId, $catatan) {
            $saldo = $barang->stok;

            $barang->lokasiRak()->where('lokasi_rak_id', $dariLokasiId)
                ->update(['qty' => DB::raw("qty - $qty")]);
            $barang->lokasiRak()->updateOrCreate(
                ['lokasi_rak_id' => $keLokasiId],
                ['qty' => DB::raw("qty + $qty")]
            );

            $this->catatTransaksi($barang, 'mutasi_keluar', $qty, $saldo, $catatan, null, $dariLokasiId);
            $this->catatTransaksi($barang, 'mutasi_masuk', $qty, $saldo, $catatan, null, $keLokasiId);
        });
    }

    public function koreksi(Barang $barang, int $stokFisik, string $catatan = null): StokTransaksi
    {
        return DB::transaction(function () use ($barang, $stokFisik, $catatan) {
            $saldoSebelum = $barang->stok;
            $selisih = $stokFisik - $saldoSebelum;
            $barang->update(['stok' => $stokFisik]);

            return $this->catatTransaksi($barang, 'koreksi', $selisih, $saldoSebelum, $catatan);
        });
    }

    private function catatTransaksi(
        Barang $barang,
        string $tipe,
        int $qty,
        int $saldoSebelum,
        string $catatan = null,
        $referensi = null,
        string $lokasiRakId = null
    ): StokTransaksi {
        return StokTransaksi::create([
            'barang_id'      => $barang->id,
            'lokasi_rak_id'  => $lokasiRakId,
            'user_id'        => Auth::id(),
            'tipe'           => $tipe,
            'qty'            => $qty,
            'saldo_sebelum'  => $saldoSebelum,
            'saldo_sesudah'  => $barang->fresh()->stok,
            'referensi_id'   => $referensi?->id,
            'referensi_type' => $referensi ? get_class($referensi) : null,
            'catatan'        => $catatan,
        ]);
    }
}
