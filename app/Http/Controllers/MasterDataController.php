<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\LokasiRak;
use App\Models\MasterTarifJasa;
use App\Models\PaketServis;
use Illuminate\Http\Request;
class MasterDataController extends Controller
{
    // ── Kategori Barang ──────────────────────────────────────────────────────

    public function kategoriIndex()
    {
        return view('master.kategori', ['kategori' => KategoriBarang::withCount('barang')->latest()->get()]);
    }

    public function kategoriStore(Request $request)
    {
        $request->validate(['nama' => 'required|string|unique:kategori_barang', 'deskripsi' => 'nullable|string']);
        KategoriBarang::create($request->only('nama', 'deskripsi'));
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function kategoriUpdate(Request $request, KategoriBarang $kategori)
    {
        $request->validate(['nama' => 'required|string|unique:kategori_barang,nama,' . $kategori->id]);
        $kategori->update($request->only('nama', 'deskripsi'));
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    // ── Lokasi Rak ───────────────────────────────────────────────────────────

    public function rakIndex()
    {
        return view('master.rak', ['rak' => LokasiRak::withCount('barangLokasi')->latest()->get()]);
    }

    public function rakStore(Request $request)
    {
        $request->validate(['kode' => 'required|string|unique:lokasi_rak', 'deskripsi' => 'nullable|string', 'kapasitas' => 'nullable|integer']);
        LokasiRak::create($request->only('kode', 'deskripsi', 'kapasitas'));
        return back()->with('success', 'Lokasi rak berhasil ditambahkan.');
    }

    public function rakUpdate(Request $request, LokasiRak $rak)
    {
        $request->validate(['deskripsi' => 'nullable|string', 'kapasitas' => 'nullable|integer', 'is_frozen' => 'boolean']);
        $rak->update($request->only('deskripsi', 'kapasitas', 'is_frozen'));
        return back()->with('success', 'Lokasi rak berhasil diperbarui.');
    }

    // ── Tarif Jasa ───────────────────────────────────────────────────────────

    public function tarifIndex()
    {
        return view('master.tarif', ['tarif' => MasterTarifJasa::latest()->get()]);
    }

    public function tarifStore(Request $request)
    {
        $request->validate(['nama' => 'required|string', 'tarif' => 'required|numeric|min:0', 'deskripsi' => 'nullable|string']);
        MasterTarifJasa::create($request->only('nama', 'tarif', 'deskripsi'));
        return back()->with('success', 'Tarif jasa berhasil ditambahkan.');
    }

    public function tarifUpdate(Request $request, MasterTarifJasa $tarif)
    {
        $request->validate(['nama' => 'required|string', 'tarif' => 'required|numeric|min:0', 'is_active' => 'boolean']);
        $tarif->update($request->only('nama', 'tarif', 'deskripsi', 'is_active'));
        return back()->with('success', 'Tarif jasa berhasil diperbarui.');
    }

    // ── Paket Servis ─────────────────────────────────────────────────────────

    public function paketIndex()
    {
        return view('master.paket', ['paket' => PaketServis::with('detail.barang', 'detail.tarifJasa')->latest()->get()]);
    }

    public function paketStore(Request $request)
    {
        $data = $request->validate([
            'nama'        => 'required|string',
            'harga_paket' => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string',
            'items'       => 'array',
            'items.*.barang_id'     => 'nullable|uuid|exists:barang,id',
            'items.*.tarif_jasa_id' => 'nullable|uuid|exists:master_tarif_jasa,id',
            'items.*.qty'           => 'required_with:items|integer|min:1',
            'items.*.harga'         => 'required_with:items|numeric|min:0',
        ]);

        $paket = PaketServis::create($request->only('nama', 'harga_paket', 'deskripsi'));

        foreach ($data['items'] ?? [] as $item) {
            $paket->detail()->create($item);
        }

        return back()->with('success', 'Paket servis berhasil ditambahkan.');
    }
}
