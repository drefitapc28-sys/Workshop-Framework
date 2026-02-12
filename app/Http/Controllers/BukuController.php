<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bukus = Buku::with('kategori')->paginate(10);
        return view('buku.index', compact('bukus'));
    }

   
    public function create()
    {
        $kategoris = Kategori::all();
        return view('buku.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:buku,kode|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required|exists:kategori,idkategori',
        ]);

        Buku::create($validated);

        return redirect()->route('buku.index')
                        ->with('success', 'Buku berhasil ditambahkan');
    }

    public function show(Buku $buku)
    {
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $kategoris = Kategori::all();
        return view('buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:buku,kode,' . $buku->idbuku . ',idbuku|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required|exists:kategori,idkategori',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')
                        ->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('buku.index')
                        ->with('success', 'Buku berhasil dihapus');
    }
}
