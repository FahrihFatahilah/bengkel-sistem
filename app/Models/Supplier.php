<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'supplier';

    protected $fillable = ['nama', 'kontak', 'alamat', 'email', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'supplier_utama_id');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}
