<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Supplier::withCount('pembelian')
            ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('supplier.index', ['supplier' => $supplier, 'filters' => $request->only(['search'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'    => 'required|string',
            'kontak'  => 'nullable|string',
            'alamat'  => 'nullable|string',
            'email'   => 'nullable|email',
        ]);

        Supplier::create($data);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'nama'      => 'required|string',
            'kontak'    => 'nullable|string',
            'alamat'    => 'nullable|string',
            'email'     => 'nullable|email',
            'is_active' => 'boolean',
        ]);

        $supplier->update($data);

        return back()->with('success', 'Supplier berhasil diperbarui.');
    }

    public function show(Supplier $supplier)
    {
        return view('supplier.show', ['supplier' => $supplier, 'pembelian' => $supplier->pembelian()->with(['user', 'detail.barang'])->latest()->paginate(20)]);
    }
}
