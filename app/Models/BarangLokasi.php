<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BarangLokasi extends Model
{
    use HasUuids;

    protected $table = 'barang_lokasi';
    protected $fillable = ['barang_id', 'lokasi_rak_id', 'qty'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasiRak()
    {
        return $this->belongsTo(LokasiRak::class);
    }
}
