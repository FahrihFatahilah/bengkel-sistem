<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;
class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barang = Barang::with(['kategori', 'supplierUtama'])
            ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%")
                ->orWhere('kode', 'like', "%{$request->search}%"))
            ->when($request->kategori_id, fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->when($request->stok_kritis, fn($q) => $q->whereColumn('stok', '<=', 'stok_minimum'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('barang.index', [
            'barang'    => $barang,
            'kategori'  => KategoriBarang::all(),
            'filters'   => $request->only(['search', 'kategori_id', 'stok_kritis']),
        ]);
    }

    public function create()
    {
        return view('barang.form', [
            'kategori' => KategoriBarang::all(),
            'supplier' => Supplier::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'              => 'required|string|unique:barang',
            'nama'              => 'required|string',
            'kategori_id'       => 'nullable|uuid|exists:kategori_barang,id',
            'satuan'            => 'required|string',
            'harga_beli'        => 'required|numeric|min:0',
            'harga_jual'        => 'required|numeric|min:0',
            'stok_minimum'      => 'required|integer|min:0',
            'supplier_utama_id' => 'nullable|uuid|exists:supplier,id',
        ]);

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.form', [
            'barang'   => $barang->load(['kategori', 'supplierUtama', 'lokasiRak.lokasiRak']),
            'kategori' => KategoriBarang::all(),
            'supplier' => Supplier::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'nama'              => 'required|string',
            'kategori_id'       => 'nullable|uuid|exists:kategori_barang,id',
            'satuan'            => 'required|string',
            'harga_beli'        => 'required|numeric|min:0',
            'harga_jual'        => 'required|numeric|min:0',
            'stok_minimum'      => 'required|integer|min:0',
            'supplier_utama_id' => 'nullable|uuid|exists:supplier,id',
            'is_active'         => 'boolean',
        ]);

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function show(Barang $barang)
    {
        return view('barang.show', [
            'barang' => $barang->load(['kategori', 'supplierUtama', 'lokasiRak.lokasiRak',
                'stokTransaksi' => fn($q) => $q->with('user')->latest()->limit(50)]),
        ]);
    }
}
