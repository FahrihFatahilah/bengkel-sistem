<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Kendaraan extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'kendaraan';

    protected $fillable = ['pelanggan_id', 'nomor_polisi', 'merek', 'tipe', 'tahun'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function workOrder()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
