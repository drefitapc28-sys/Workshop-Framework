<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BarcodeService;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all(); // Mengambil semua data barang dari database menggunakan model Barang
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
        $barang = Barang::whereIn('id_barang', $request->barang)->get(); //mengambil hanya barang yang dicentang user

        $startIndex = (($request->start_y - 1) * 5) + $request->start_x; 
        // dikali 5 karena 1 barisd = 5 kolom 
        // posisi = (baris - 1) * jumlah + kolom
        // misal start_y(baris) = 2, start_x = 3 
        // posisi = (2 - 1) * 5 + 3 = 8 (posisi di baris ke-2, kolom ke-3) atau dimulai dari label ke-8

        $labels = array_fill(1, 40, null);
        // Mengisi array labels dengan data barang yang dipilih, dimulai dari posisi yang dihitung
        // membuat  array 1 - 40 

        foreach ($barang as $index => $item) {
            if (($startIndex + $index) <= 40) {
                $item->barcode = BarcodeService::generateBase64($item->id_barang);
                $labels[$startIndex + $index] = $item;
            }
        }

        $pdf = Pdf::loadView('barang.pdf', compact('labels')); // loadview untuk memanggil file pdf.blade.php dan mengirim data labels ke view tersebut
        return $pdf->stream('tag-harga.pdf'); //stream untuk langsung tampil di browser, download untuk mengunduh

    }
}
