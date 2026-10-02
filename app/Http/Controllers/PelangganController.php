<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Pelanggan::withCount('kendaraan')
            ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%")
                ->orWhere('no_hp', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pelanggan.index', ['pelanggan' => $pelanggan, 'filters' => $request->only(['search'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'   => 'required|string',
            'no_hp'  => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $pelanggan = Pelanggan::create($data);

        return back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nama'   => 'required|string',
            'no_hp'  => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $pelanggan->update($data);

        return back()->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function show(Pelanggan $pelanggan)
    {
        return view('pelanggan.show', ['pelanggan' => $pelanggan->load(['kendaraan.workOrder' => fn($q) => $q->with('detail')->latest()->limit(10)])]);
    }

    public function storeKendaraan(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nomor_polisi' => 'required|string|unique:kendaraan',
            'merek'        => 'nullable|string',
            'tipe'         => 'nullable|string',
            'tahun'        => 'nullable|integer|min:1990|max:' . date('Y'),
        ]);

        $pelanggan->kendaraan()->create($data);

        return back()->with('success', 'Kendaraan berhasil ditambahkan.');
    }
}
