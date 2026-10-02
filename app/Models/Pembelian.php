<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pembelian extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'pembelian';

    protected $fillable = ['nomor', 'supplier_id', 'user_id', 'referensi_po', 'tanggal', 'total', 'catatan'];
    protected $casts = ['tanggal' => 'date', 'total' => 'decimal:2'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
