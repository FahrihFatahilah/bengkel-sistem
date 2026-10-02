<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class KategoriBarang extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'kategori_barang';
    protected $fillable = ['nama', 'deskripsi'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
