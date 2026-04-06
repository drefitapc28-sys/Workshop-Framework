<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    // Dashboard utama
    public function index()
    {
        $vendor = Auth::user()->vendor;

        $pesanans = Pesanan::with(['detailPesanans.menu'])
            ->where('idvendor', $vendor->idvendor)
            ->where('status_bayar', 'lunas')  
            ->orderByDesc('created_at')
            ->paginate(10);

        $totalMenu = Menu::where('idvendor', $vendor->idvendor)->count();

        return view('vendor.index', compact('vendor', 'pesanans', 'totalMenu'));
    }

    // ===== MENU =====
    public function listMenu()
    {
        $vendor = Auth::user()->vendor;
        $menus = Menu::where('idvendor', $vendor->idvendor)->paginate(10);
        return view('vendor.menu-list', compact('menus'));
    }

    public function createMenu()
    {
        return view('vendor.menu-form');
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu'    => 'required|string|max:255',
            'harga'        => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string|max:500',
            'path_gambar'  => 'nullable|image|max:2048',
        ]);

        $vendor = Auth::user()->vendor;
        $pathGambar = null;

        if ($request->hasFile('path_gambar')) {
            $pathGambar = $request->file('path_gambar')->store('menu', 'public');
        }

        Menu::create([
            'nama_menu'    => $request->nama_menu,
            'harga'        => $request->harga,
            'deskripsi'    => $request->deskripsi,
            'path_gambar'  => $pathGambar,
            'idvendor'     => $vendor->idvendor,
        ]);

        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function editMenu($idmenu)
    {
        $vendor = Auth::user()->vendor;
        $menu = Menu::where('idmenu', $idmenu)->where('idvendor', $vendor->idvendor)->firstOrFail();
        return view('vendor.menu-form', compact('menu'));
    }

    public function updateMenu(Request $request, $idmenu)
    {
        $request->validate([
            'nama_menu'    => 'required|string|max:255',
            'harga'        => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string|max:500',
            'path_gambar'  => 'nullable|image|max:2048',
        ]);

        $vendor = Auth::user()->vendor;
        $menu = Menu::where('idmenu', $idmenu)->where('idvendor', $vendor->idvendor)->firstOrFail();

        if ($request->hasFile('path_gambar')) {
            if ($menu->path_gambar) {
                Storage::disk('public')->delete($menu->path_gambar);
            }
            $menu->path_gambar = $request->file('path_gambar')->store('menu', 'public');
        }

        $menu->update([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'deskripsi'   => $request->deskripsi,
        ]);

        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil diupdate!');
    }

    public function deleteMenu($idmenu)
    {
        $vendor = Auth::user()->vendor;
        $menu = Menu::where('idmenu', $idmenu)->where('idvendor', $vendor->idvendor)->firstOrFail();
        
        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }
        
        $menu->delete();
        return redirect()->route('vendor.menus')->with('success', 'Menu berhasil dihapus!');
    }

    // ===== PESANAN =====
    public function detailPesanan($idpesanan)
    {
        $vendor = Auth::user()->vendor;
        $pesanan = Pesanan::with(['detailPesanans.menu', 'vendor'])
            ->where('idpesanan', $idpesanan)
            ->where('idvendor', $vendor->idvendor)
            ->firstOrFail();
        return view('vendor.detail-pesanan', compact('pesanan'));
    }
}