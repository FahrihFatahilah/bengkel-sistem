<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\MasterTarifJasa;
use App\Models\Pelanggan;
use App\Models\WorkOrder;
use App\Models\WorkOrderDetail;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
class WorkOrderController extends Controller
{
    public function __construct(private WorkOrderService $woService) {}

    public function index(Request $request)
    {
        $workOrders = WorkOrder::with(['kendaraan', 'pelanggan', 'mekanik'])
            ->when($request->search, fn($q) => $q->where('nomor', 'like', "%{$request->search}%")
                ->orWhereHas('kendaraan', fn($q) => $q->where('nomor_polisi', 'like', "%{$request->search}%")))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('work-order.index', ['workOrders' => $workOrders, 'filters' => $request->only(['search', 'status'])]);
    }

    public function create()
    {
        return view('work-order.create');
    }

    public function cariKendaraan(Request $request)
    {
        $nopol = strtoupper(trim($request->nopol));
        $kendaraan = Kendaraan::with('pelanggan')
            ->where('nomor_polisi', $nopol)
            ->first();

        if (!$kendaraan) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'        => true,
            'kendaraan_id' => $kendaraan->id,
            'pelanggan_id' => $kendaraan->pelanggan_id,
            'nama'         => $kendaraan->pelanggan?->nama,
            'no_hp'        => $kendaraan->pelanggan?->no_hp,
            'merek'        => $kendaraan->merek,
            'tipe'         => $kendaraan->tipe,
            'tahun'        => $kendaraan->tahun,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_polisi'   => 'required|string',
            'nama_pelanggan' => 'required|string',
            'keluhan'        => 'required|string',
        ]);

        // Cari atau buat kendaraan & pelanggan
        $kendaraan = Kendaraan::where('nomor_polisi', strtoupper($request->nomor_polisi))->first();

        if (!$kendaraan) {
            $pelanggan = Pelanggan::create([
                'nama'      => $request->nama_pelanggan,
                'no_hp'     => $request->no_hp,
                'is_active' => true,
            ]);
            $kendaraan = Kendaraan::create([
                'pelanggan_id'  => $pelanggan->id,
                'nomor_polisi'  => strtoupper($request->nomor_polisi),
                'merek'         => $request->merek,
                'tipe'          => $request->tipe,
                'tahun'         => $request->tahun,
            ]);
        }

        $wo = $this->woService->buatWorkOrder([
            'kendaraan_id' => $kendaraan->id,
            'pelanggan_id' => $kendaraan->pelanggan_id,
            'keluhan'      => $request->keluhan,
            'is_estimasi'  => false,
        ]);

        return redirect()->route('work-order.show', $wo)->with('success', 'Work Order berhasil dibuat. Silakan tambah item pekerjaan.');
    }

    public function show(WorkOrder $workOrder)
    {
        $kategori = \App\Models\KategoriBarang::with(['barang' => fn($q) => $q->where('is_active', true)->select('id','kode','nama','satuan','harga_jual','stok','kategori_id')])->get();

        return view('work-order.show', [
            'workOrder' => $workOrder->load(['kendaraan.pelanggan', 'pelanggan', 'mekanik', 'kasir', 'detail.barang', 'detail.tarifJasa']),
            'kategori'  => $kategori,
            'tarifJasa' => \App\Models\MasterTarifJasa::where('is_active', true)->get(),
        ]);
    }

    public function tambahItem(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'barang_id'     => 'nullable|uuid|exists:barang,id',
            'tarif_jasa_id' => 'nullable|uuid|exists:master_tarif_jasa,id',
            'nama_item'     => 'required|string',
            'harga_barang'  => 'required|numeric|min:0',
            'tarif_pasang'  => 'required|numeric|min:0',
            'qty'           => 'required|integer|min:1',
        ]);

        $detail = $this->woService->tambahItem($workOrder, $data);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function updateItem(Request $request, WorkOrder $workOrder, WorkOrderDetail $detail)
    {
        $data = $request->validate([
            'harga_barang' => 'numeric|min:0',
            'tarif_pasang' => 'numeric|min:0',
            'qty'          => 'integer|min:1',
        ]);

        $this->woService->updateItem($detail, $data);

        return back()->with('success', 'Item berhasil diperbarui.');
    }

    public function menungguPembayaran(WorkOrder $workOrder)
    {
        $this->woService->menungguPembayaran($workOrder);
        return back()->with('success', 'Work Order siap dibayar.');
    }

    public function bayar(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'diskon'      => 'numeric|min:0',
            'metode_bayar' => 'required|in:tunai,transfer,qris',
        ]);

        $this->woService->prosesPembayaran($workOrder, $data);

        return redirect()->route('work-order.show', $workOrder)->with('success', 'Pembayaran berhasil.');
    }

    public function batalkan(Request $request, WorkOrder $workOrder)
    {
        $request->validate(['catatan' => 'required|string']);
        $this->woService->batalkan($workOrder, $request->catatan);
        return redirect()->route('work-order.index')->with('success', 'Work Order dibatalkan.');
    }

    public function hapusItem(WorkOrder $workOrder, WorkOrderDetail $detail)
    {
        $this->woService->hapusItem($workOrder, $detail);
        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function serahTerima(Request $request, WorkOrder $workOrder, WorkOrderDetail $detail)
    {
        $detail->update(['status_serah_terima' => 'sudah']);
        return back()->with('success', 'Barang diserahkan.');
    }

    public function konfirmasiEstimasi(WorkOrder $workOrder)
    {
        $this->woService->prosesKonfirmasiEstimasi($workOrder);
        return back()->with('success', 'Estimasi dikonfirmasi, stok dikurangi.');
    }
}
