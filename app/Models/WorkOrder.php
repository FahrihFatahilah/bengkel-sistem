<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkOrder extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'work_order';
    protected $fillable = [
        'nomor', 'kendaraan_id', 'pelanggan_id', 'mekanik_id', 'kasir_id',
        'keluhan', 'diagnosa', 'status', 'total', 'diskon', 'total_bayar',
        'metode_bayar', 'approved_diskon_by', 'tanggal_masuk', 'tanggal_selesai',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'tanggal_masuk' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function mekanik()
    {
        return $this->belongsTo(User::class, 'mekanik_id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detail()
    {
        return $this->hasMany(WorkOrderDetail::class);
    }

    public function isLocked(): bool
    {
        return $this->status === 'lunas';
    }
}
