<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OpnameDetail extends Model
{
    use HasUuids;

    protected $table = 'opname_detail';
    protected $fillable = [
        'opname_sesi_id', 'barang_id', 'lokasi_rak_id',
        'stok_sistem', 'stok_fisik', 'selisih', 'nilai_selisih',
    ];

    protected $casts = ['nilai_selisih' => 'decimal:2'];

    public function opnameSesi()
    {
        return $this->belongsTo(OpnameSesi::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasiRak()
    {
        return $this->belongsTo(LokasiRak::class);
    }
}
