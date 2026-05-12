<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\KunjunganToko;
use Illuminate\Http\Request;
use Auth;

class KunjunganTokoController extends Controller
{
    // Threshold jarak dalam meter
    private $THRESHOLD = 300;

    public function index()
    {
        $tokoList = Toko::all();
        return view('kunjungan-toko.index', compact('tokoList'));
    }

    /* Dapatkan daftar toko dalam format JSON */
    public function getTokoList()
    {
        $toko = Toko::all();
        return response()->json($toko);
    }

    /* Simpan titik awal toko */
    public function storeToko(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|unique:toko,barcode',
            'nama_toko' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:1',
        ]);

        $toko = Toko::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil disimpan',
            'data' => $toko
        ], 201);
    }

    /* Verifikasi kunjungan toko oleh sales */
    public function verifyKunjungan(Request $request)
    {
        $validated = $request->validate([
            'toko_id' => 'required|exists:toko,id',
            'latitude_sales' => 'required|numeric|between:-90,90',
            'longitude_sales' => 'required|numeric|between:-180,180',
            'accuracy_sales' => 'required|numeric|min:1',
        ]);

        // Ambil data toko
        $toko = Toko::findOrFail($validated['toko_id']);

        // Hitung jarak menggunakan Haversine
        $jarak = $this->calculateHaversine(
            $toko->latitude,
            $toko->longitude,
            $validated['latitude_sales'],
            $validated['longitude_sales']
        );

        // Tentukan threshold efektif
        $threshold_efektif = $this->THRESHOLD + $toko->accuracy + $validated['accuracy_sales'];

        // Validasi jarak
        $status = ($jarak <= $threshold_efektif) ? 'diterima' : 'ditolak';

        // Simpan kunjungan
        $kunjungan = KunjunganToko::create([
            'user_id' => Auth::id(),
            'toko_id' => $validated['toko_id'],
            'latitude_sales' => $validated['latitude_sales'],
            'longitude_sales' => $validated['longitude_sales'],
            'accuracy_sales' => $validated['accuracy_sales'],
            'jarak_aktual' => $jarak,
            'status_kunjungan' => $status,
        ]);

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $status === 'diterima' 
                ? 'Kunjungan diterima ✓' 
                : 'Kunjungan ditolak - Anda terlalu jauh dari toko',
            'data' => [
                'toko_nama' => $toko->nama_toko,
                'toko_latitude' => $toko->latitude,
                'toko_longitude' => $toko->longitude,
                'toko_accuracy' => $toko->accuracy,
                'sales_latitude' => $validated['latitude_sales'],
                'sales_longitude' => $validated['longitude_sales'],
                'sales_accuracy' => $validated['accuracy_sales'],
                'jarak_aktual' => round($jarak, 2),
                'threshold_efektif' => $threshold_efektif,
            ]
        ]);
    }

    public function destroyToko($id)
    {
        $toko = Toko::findOrFail($id);
        $toko->delete();

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil dihapus'
        ]);
    }

    /**
     * Hitung jarak menggunakan formula Haversine
     * @param float $lat1 Latitude toko
     * @param float $lng1 Longitude toko
     * @param float $lat2 Latitude sales
     * @param float $lng2 Longitude sales
     * @return float Jarak dalam meter
     */
    private function calculateHaversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }
}
