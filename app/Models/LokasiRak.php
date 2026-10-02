<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LokasiRak extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'lokasi_rak';
    protected $fillable = ['kode', 'deskripsi', 'kapasitas', 'is_frozen'];
    protected $casts = ['is_frozen' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function barangLokasi()
    {
        return $this->hasMany(BarangLokasi::class);
    }
}
