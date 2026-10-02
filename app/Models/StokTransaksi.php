<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StokTransaksi extends Model
{
    use HasUuids;

    protected $table = 'stok_transaksi';
    protected $fillable = [
        'barang_id', 'lokasi_rak_id', 'user_id', 'tipe',
        'qty', 'saldo_sebelum', 'saldo_sesudah',
        'referensi_id', 'referensi_type', 'catatan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasiRak()
    {
        return $this->belongsTo(LokasiRak::class);
    }

    public function referensi()
    {
        return $this->morphTo();
    }
}
