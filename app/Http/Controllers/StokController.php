<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokTransaksi;
use App\Models\LokasiRak;
use App\Services\StockService;
use Illuminate\Http\Request;
class StokController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function kartuStok(Request $request, Barang $barang)
    {
        $transaksi = StokTransaksi::where('barang_id', $barang->id)
            ->with(['user', 'lokasiRak'])
            ->when($request->dari, fn($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->sampai, fn($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('stok.kartu', ['barang' => $barang->load('kategori'), 'transaksi' => $transaksi, 'filters' => $request->only(['dari', 'sampai'])]);
    }

    public function mutasi(Request $request)
    {
        $data = $request->validate([
            'barang_id'       => 'required|uuid|exists:barang,id',
            'dari_lokasi_id'  => 'required|uuid|exists:lokasi_rak,id',
            'ke_lokasi_id'    => 'required|uuid|exists:lokasi_rak,id|different:dari_lokasi_id',
            'qty'             => 'required|integer|min:1',
            'catatan'         => 'nullable|string',
        ]);

        $barang = Barang::findOrFail($data['barang_id']);
        $this->stockService->mutasi($barang, $data['qty'], $data['dari_lokasi_id'], $data['ke_lokasi_id'], $data['catatan'] ?? null);

        return back()->with('success', 'Mutasi stok berhasil.');
    }

    public function indexMutasi()
    {
        return view('stok.mutasi', ['barang' => Barang::where('is_active', true)->get(['id', 'kode', 'nama', 'stok', 'satuan']), 'rak' => LokasiRak::all()]);
    }
}
