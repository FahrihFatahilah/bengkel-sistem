<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PembelianController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $pembelian = Pembelian::with(['supplier', 'user'])
            ->when($request->search, fn($q) => $q->where('nomor', 'like', "%{$request->search}%"))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pembelian.index', ['pembelian' => $pembelian, 'supplier' => Supplier::where('is_active', true)->get(), 'filters' => $request->only(['search', 'supplier_id'])]);
    }

    public function create()
    {
        return view('pembelian.create', ['supplier' => Supplier::where('is_active', true)->get(), 'barang' => Barang::where('is_active', true)->get(['id', 'kode', 'nama', 'satuan', 'harga_beli'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'        => 'required|uuid|exists:supplier,id',
            'referensi_po'       => 'nullable|string',
            'tanggal'            => 'required|date',
            'catatan'            => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.barang_id'  => 'required|uuid|exists:barang,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum(fn($i) => $i['qty'] * $i['harga_beli']);

            $pembelian = Pembelian::create([
                'nomor'        => $this->generateNomor(),
                'supplier_id'  => $data['supplier_id'],
                'user_id'      => auth()->id(),
                'referensi_po' => $data['referensi_po'] ?? null,
                'tanggal'      => $data['tanggal'],
                'total'        => $total,
                'catatan'      => $data['catatan'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $pembelian->detail()->create([
                    'barang_id'  => $item['barang_id'],
                    'qty'        => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal'   => $item['qty'] * $item['harga_beli'],
                ]);

                $barang = Barang::find($item['barang_id']);
                $this->stockService->masuk($barang, $item['qty'], "Pembelian #{$pembelian->nomor}", $pembelian);
            }
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Pembelian $pembelian)
    {
        return view('pembelian.show', ['pembelian' => $pembelian->load(['supplier', 'user', 'detail.barang'])]);
    }

    private function generateNomor(): string
    {
        $prefix = 'PO-' . date('Ymd');
        $last = Pembelian::where('nomor', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (int) substr($last->nomor, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
