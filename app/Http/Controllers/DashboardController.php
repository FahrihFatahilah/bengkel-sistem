<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        $stokKritis = Barang::whereColumn('stok', '<=', 'stok_minimum')
            ->with('kategori')
            ->limit(10)
            ->get();

        $antriPembayaran = WorkOrder::where('status', 'menunggu_pembayaran')
            ->with(['kendaraan', 'pelanggan', 'mekanik'])
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total_barang'        => Barang::where('is_active', true)->count(),
            'stok_kritis'         => Barang::whereColumn('stok', '<=', 'stok_minimum')->count(),
            'wo_proses'           => WorkOrder::where('status', 'proses')->count(),
            'wo_menunggu_bayar'   => WorkOrder::where('status', 'menunggu_pembayaran')->count(),
        ];

        return view('dashboard', compact('stokKritis', 'antriPembayaran', 'stats'));
    }
}
