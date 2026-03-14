<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /* Tampilkan halaman POS */
    public function index()
    {
        return view('pos.index');
    }

    /*GET data barang berdasarkan kode
      Endpoint: GET /pos/barang/{kode}
    */
    public function getBarang($kode)
    {
        $barang = DB::table('barang')
            ->where('id_barang', $kode)
            ->first();

        if (!$barang) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Barang tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Barang ditemukan',
            'data'    => $barang
        ]);
    }

    /* POST simpan transaksi penjualan
       Endpoint: POST /pos/bayar
    */
    public function bayar(Request $req)
    {
        // Validasi input
        $req->validate([
            'total'   => 'required|numeric|min:1',
            'items'   => 'required|array|min:1',
            'items.*.id_barang' => 'required|string',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.subtotal'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Insert ke tabel penjualan
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(),
                'total'     => $req->total,
            ], 'id_penjualan');  // Specify primary key name

            // 2. Insert ke tabel penjualan_detail (tiap item)
            $details = [];
            foreach ($req->items as $item) {
                $details[] = [
                    'id_penjualan' => $id_penjualan,
                    'id_barang'    => $item['id_barang'],
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal'],
                ];
            }
            DB::table('penjualan_detail')->insert($details);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Transaksi berhasil disimpan',
                'data'    => ['id_penjualan' => $id_penjualan]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('POS Bayar Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }
}