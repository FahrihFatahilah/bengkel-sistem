<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PaketServis extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'paket_servis';
    protected $fillable = ['nama', 'harga_paket', 'deskripsi', 'is_active'];
    protected $casts = ['harga_paket' => 'decimal:2', 'is_active' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function detail()
    {
        return $this->hasMany(PaketServisDetail::class);
    }
}
