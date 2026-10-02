<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaketServisDetail extends Model
{
    use HasUuids;

    protected $table = 'paket_servis_detail';
    protected $fillable = ['paket_servis_id', 'barang_id', 'tarif_jasa_id', 'qty', 'harga'];
    protected $casts = ['harga' => 'decimal:2'];

    public function paketServis()
    {
        return $this->belongsTo(PaketServis::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function tarifJasa()
    {
        return $this->belongsTo(MasterTarifJasa::class, 'tarif_jasa_id');
    }
}
