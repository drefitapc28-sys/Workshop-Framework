<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        Barang::create($request->only('nama','harga'));
        return redirect()->route('barang.index');
    }

    public function edit($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        $barang->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id_barang)
    {
        Barang::destroy($id_barang);
        return back()->with('success', 'Barang berhasil dihapus');
    }

    public function formCetak()
    {
        $barang = Barang::all();
        return view('barang.form-cetak', compact('barang'));
    }

    public function cetak(Request $request)
    {
        $barang = Barang::whereIn('id_barang', $request->barang)->get();

        $startIndex = (($request->start_y - 1) * 5) + $request->start_x;

        $labels = array_fill(1, 40, null);

        foreach ($barang as $index => $item) {
            if (($startIndex + $index) <= 40) {
                $labels[$startIndex + $index] = $item;
            }
        }

        $pdf = Pdf::loadView('barang.pdf', compact('labels'));
        return $pdf->stream('tag-harga.pdf'); //stream untuk langsung tampil di browser, download untuk mengunduh

    }
}
