<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalBuku = Buku::count();
        $totalKategori = Kategori::count();
        $totalPengguna = User::count();
        
        return view('home', [
            'totalBuku' => $totalBuku,
            'totalKategori' => $totalKategori,
            'totalPengguna' => $totalPengguna,
        ]);
    }
}
