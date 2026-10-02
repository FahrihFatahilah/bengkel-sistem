<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LokasiRak;
use App\Models\OpnameDetail;
use App\Models\OpnameSesi;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OpnameController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index()
    {
        return view('opname.index', ['sesi' => OpnameSesi::with(['dibuatOleh', 'disetujuiOleh'])->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('opname.create', ['barang' => Barang::where('is_active', true)->with('lokasiRak.lokasiRak')->get(['id', 'kode', 'nama', 'stok', 'satuan']), 'rak' => LokasiRak::where('is_frozen', false)->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'catatan'              => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.barang_id'    => 'required|uuid|exists:barang,id',
            'items.*.lokasi_rak_id'=> 'nullable|uuid|exists:lokasi_rak,id',
            'items.*.stok_fisik'   => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $sesi = OpnameSesi::create([
                'nomor'       => $this->generateNomor(),
                'dibuat_oleh' => auth()->id(),
                'catatan'     => $data['catatan'] ?? null,
                'status'      => 'draft',
            ]);

            foreach ($data['items'] as $item) {
                $barang = Barang::find($item['barang_id']);
                $sesi->detail()->create([
                    'barang_id'     => $item['barang_id'],
                    'lokasi_rak_id' => $item['lokasi_rak_id'] ?? null,
                    'stok_sistem'   => $barang->stok,
                    'stok_fisik'    => $item['stok_fisik'],
                    'selisih'       => $item['stok_fisik'] - $barang->stok,
                    'nilai_selisih' => ($item['stok_fisik'] - $barang->stok) * $barang->harga_beli,
                ]);
            }
        });

        return redirect()->route('opname.index')->with('success', 'Opname berhasil dibuat.');
    }

    public function show(OpnameSesi $opname)
    {
        return view('opname.show', ['sesi' => $opname->load(['dibuatOleh', 'disetujuiOleh', 'detail.barang', 'detail.lokasiRak'])]);
    }

    public function ajukan(OpnameSesi $opname)
    {
        if ($opname->status !== 'draft') abort(403);
        $opname->update(['status' => 'diajukan']);
        return back()->with('success', 'Opname diajukan untuk approval.');
    }

    public function approve(Request $request, OpnameSesi $opname)
    {
        if ($opname->status !== 'diajukan') abort(403);

        $request->validate(['action' => 'required|in:setujui,tolak', 'catatan_penolakan' => 'required_if:action,tolak|string']);

        DB::transaction(function () use ($request, $opname) {
            if ($request->action === 'setujui') {
                foreach ($opname->detail as $detail) {
                    if ($detail->selisih !== 0) {
                        $barang = Barang::find($detail->barang_id);
                        $this->stockService->koreksi($barang, $detail->stok_fisik, "Opname #{$opname->nomor}");
                    }
                }
                $opname->update([
                    'status'          => 'disetujui',
                    'disetujui_oleh'  => auth()->id(),
                    'tanggal_selesai' => now(),
                ]);
            } else {
                $opname->update([
                    'status'              => 'ditolak',
                    'catatan_penolakan'   => $request->catatan_penolakan,
                ]);
            }
        });

        return back()->with('success', 'Opname berhasil ' . ($request->action === 'setujui' ? 'disetujui' : 'ditolak') . '.');
    }

    private function generateNomor(): string
    {
        $prefix = 'OPN-' . date('Ymd');
        $last = OpnameSesi::where('nomor', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (int) substr($last->nomor, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
