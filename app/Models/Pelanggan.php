<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pelanggan extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'pelanggan';

    protected $fillable = ['nama', 'no_hp', 'alamat', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function workOrder()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
