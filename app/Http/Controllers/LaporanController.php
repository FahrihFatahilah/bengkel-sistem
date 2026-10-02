<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\OpnameSesi;
use App\Models\Pembelian;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class LaporanController extends Controller
{
    public function stokMinimum()
    {
        return view('laporan.stok-minimum', ['barang' => Barang::whereColumn('stok', '<=', 'stok_minimum')->with(['kategori', 'supplierUtama'])->get()]);
    }

    public function nilaiPersediaan(Request $request)
    {
        $barang = Barang::with('kategori')
            ->when($request->kategori_id, fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'kode'          => $b->kode,
                'nama'          => $b->nama,
                'kategori'      => $b->kategori?->nama,
                'stok'          => $b->stok,
                'harga_beli'    => $b->harga_beli,
                'nilai_total'   => $b->stok * $b->harga_beli,
            ]);

        return view('laporan.nilai-persediaan', ['barang' => $barang, 'total_nilai' => $barang->sum('nilai_total'), 'filters' => $request->only(['kategori_id'])]);
    }

    public function penjualan(Request $request)
    {
        $dari   = $request->dari ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $workOrders = WorkOrder::where('status', 'lunas')
            ->whereBetween('tanggal_selesai', [$dari, $sampai . ' 23:59:59'])
            ->with(['pelanggan', 'kendaraan', 'kasir', 'detail'])
            ->latest('tanggal_selesai')
            ->get();

        $summary = [
            'total_transaksi' => $workOrders->count(),
            'total_pendapatan' => $workOrders->sum('total_bayar'),
            'total_diskon'     => $workOrders->sum('diskon'),
        ];

        return view('laporan.penjualan', ['workOrders' => $workOrders, 'summary' => $summary, 'filters' => compact('dari', 'sampai')]);
    }

    public function pembelian(Request $request)
    {
        $dari   = $request->dari ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $pembelian = Pembelian::whereBetween('tanggal', [$dari, $sampai])
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->with(['supplier', 'user', 'detail.barang'])
            ->latest()
            ->get();

        return view('laporan.pembelian', ['pembelian' => $pembelian, 'total' => $pembelian->sum('total'), 'filters' => compact('dari', 'sampai')]);
    }

    public function opname(Request $request)
    {
        $sesi = OpnameSesi::with(['dibuatOleh', 'disetujuiOleh'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('laporan.opname', ['sesi' => $sesi, 'filters' => $request->only(['status'])]);
    }
}
