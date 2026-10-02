<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Barang extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'barang';

    protected $fillable = [
        'kode', 'nama', 'kategori_id', 'satuan', 'harga_beli',
        'harga_jual', 'stok_minimum', 'stok', 'supplier_utama_id', 'foto', 'is_active',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function supplierUtama()
    {
        return $this->belongsTo(Supplier::class, 'supplier_utama_id');
    }

    public function lokasiRak()
    {
        return $this->hasMany(BarangLokasi::class);
    }

    public function stokTransaksi()
    {
        return $this->hasMany(StokTransaksi::class);
    }

    public function isStokMinimum(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }
}
