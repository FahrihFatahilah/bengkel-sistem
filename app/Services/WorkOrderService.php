<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\WorkOrder;
use App\Models\WorkOrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function __construct(private StockService $stockService) {}

    public function buatWorkOrder(array $data): WorkOrder
    {
        return DB::transaction(function () use ($data) {
            $wo = WorkOrder::create([
                'nomor'        => $this->generateNomor(),
                'kendaraan_id' => $data['kendaraan_id'],
                'pelanggan_id' => $data['pelanggan_id'],
                'mekanik_id'   => Auth::id(),
                'keluhan'      => $data['keluhan'],
                'status'       => $data['is_estimasi'] ?? false ? 'estimasi' : 'proses',
            ]);

            return $wo;
        });
    }

    public function tambahItem(WorkOrder $wo, array $data): WorkOrderDetail
    {
        return DB::transaction(function () use ($wo, $data) {
            if ($wo->isLocked()) {
                throw new \Exception('Work Order sudah terkunci.');
            }

            $hargaBarang = $data['harga_barang'] ?? 0;
            $tarifPasang = $data['tarif_pasang'] ?? 0;
            $qty = $data['qty'] ?? 1;

            if (!empty($data['barang_id']) && $wo->status !== 'estimasi') {
                $barang = Barang::findOrFail($data['barang_id']);
                $this->stockService->keluar($barang, $qty, "Work Order #{$wo->nomor}", $wo);
            }

            $detail = $wo->detail()->create([
                'barang_id'     => $data['barang_id'] ?? null,
                'tarif_jasa_id' => $data['tarif_jasa_id'] ?? null,
                'nama_item'     => $data['nama_item'],
                'harga_barang'  => $hargaBarang,
                'tarif_pasang'  => $tarifPasang,
                'qty'           => $qty,
                'subtotal'      => ($hargaBarang + $tarifPasang) * $qty,
            ]);

            $this->hitungUlangTotal($wo);

            return $detail;
        });
    }

    public function hapusItem(WorkOrder $wo, WorkOrderDetail $detail): void
    {
        DB::transaction(function () use ($wo, $detail) {
            if ($wo->isLocked() || $detail->is_locked) {
                throw new \Exception('Item sudah terkunci.');
            }

            // Kembalikan stok jika WO sudah proses (bukan estimasi)
            if ($detail->barang_id && $wo->status !== 'estimasi') {
                $barang = Barang::findOrFail($detail->barang_id);
                $this->stockService->masuk($barang, $detail->qty, "Hapus item WO #{$wo->nomor}", $wo);
            }

            $detail->delete();
            $this->hitungUlangTotal($wo);
        });
    }

    public function updateItem(WorkOrderDetail $detail, array $data): WorkOrderDetail
    {
        return DB::transaction(function () use ($detail, $data) {
            $wo = $detail->workOrder;

            if ($wo->isLocked() || $detail->is_locked) {
                throw new \Exception('Item sudah terkunci.');
            }

            $hargaBarang = $data['harga_barang'] ?? $detail->harga_barang;
            $tarifPasang = $data['tarif_pasang'] ?? $detail->tarif_pasang;
            $qty = $data['qty'] ?? $detail->qty;

            $detail->update([
                'harga_barang' => $hargaBarang,
                'tarif_pasang' => $tarifPasang,
                'qty'          => $qty,
                'subtotal'     => ($hargaBarang + $tarifPasang) * $qty,
            ]);

            $this->hitungUlangTotal($wo);

            return $detail->fresh();
        });
    }

    public function prosesKonfirmasiEstimasi(WorkOrder $wo): WorkOrder
    {
        return DB::transaction(function () use ($wo) {
            if ($wo->status !== 'estimasi') {
                throw new \Exception('Work Order bukan estimasi.');
            }

            foreach ($wo->detail as $detail) {
                if ($detail->barang_id) {
                    $barang = Barang::findOrFail($detail->barang_id);
                    $this->stockService->keluar($barang, $detail->qty, "Konfirmasi WO #{$wo->nomor}", $wo);
                }
            }

            $wo->update(['status' => 'proses']);
            return $wo->fresh();
        });
    }

    public function menungguPembayaran(WorkOrder $wo): WorkOrder
    {
        if ($wo->status !== 'proses') {
            throw new \Exception('Status Work Order tidak valid.');
        }
        $wo->update(['status' => 'menunggu_pembayaran']);
        return $wo->fresh();
    }

    public function prosesPembayaran(WorkOrder $wo, array $data): WorkOrder
    {
        return DB::transaction(function () use ($wo, $data) {
            if ($wo->status !== 'menunggu_pembayaran') {
                throw new \Exception('Work Order belum siap dibayar.');
            }

            $wo->update([
                'kasir_id'        => Auth::id(),
                'diskon'          => $data['diskon'] ?? 0,
                'total_bayar'     => $wo->total - ($data['diskon'] ?? 0),
                'metode_bayar'    => $data['metode_bayar'],
                'status'          => 'lunas',
                'tanggal_selesai' => now(),
            ]);

            $wo->detail()->update(['is_locked' => true]);

            return $wo->fresh();
        });
    }

    public function batalkan(WorkOrder $wo, string $catatan): WorkOrder
    {
        return DB::transaction(function () use ($wo, $catatan) {
            if ($wo->status === 'lunas') {
                throw new \Exception('Work Order yang sudah lunas tidak bisa dibatalkan langsung.');
            }

            if ($wo->status !== 'estimasi') {
                foreach ($wo->detail as $detail) {
                    if ($detail->barang_id) {
                        $barang = Barang::findOrFail($detail->barang_id);
                        $this->stockService->masuk($barang, $detail->qty, "Pembatalan WO #{$wo->nomor}: {$catatan}", $wo);
                    }
                }
            }

            $wo->update(['status' => 'dibatalkan']);
            return $wo->fresh();
        });
    }

    private function hitungUlangTotal(WorkOrder $wo): void
    {
        $total = $wo->detail()->sum('subtotal');
        $wo->update(['total' => $total, 'total_bayar' => $total - $wo->diskon]);
    }

    private function generateNomor(): string
    {
        $prefix = 'WO-' . date('Ymd');
        $last = WorkOrder::where('nomor', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (int) substr($last->nomor, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
