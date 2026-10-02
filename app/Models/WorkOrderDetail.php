<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkOrderDetail extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'work_order_detail';
    protected $fillable = [
        'work_order_id', 'barang_id', 'tarif_jasa_id', 'nama_item',
        'harga_barang', 'tarif_pasang', 'qty', 'subtotal',
        'status_serah_terima', 'is_locked',
    ];

    protected $casts = [
        'harga_barang' => 'decimal:2',
        'tarif_pasang' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_locked' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function tarifJasa()
    {
        return $this->belongsTo(MasterTarifJasa::class, 'tarif_jasa_id');
    }

    public function hitungSubtotal(): float
    {
        return ($this->harga_barang + $this->tarif_pasang) * $this->qty;
    }
}
