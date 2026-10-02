<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MasterTarifJasa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'master_tarif_jasa';
    protected $fillable = ['nama', 'tarif', 'deskripsi', 'is_active'];
    protected $casts = ['tarif' => 'decimal:2', 'is_active' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
