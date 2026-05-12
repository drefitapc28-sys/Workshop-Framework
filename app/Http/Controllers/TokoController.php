<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Kunjungan;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TokoController extends Controller
{
    // ===== LIST TOKO =====
    public function index()
    {
        $tokos = Toko::latest()->get();
        return view('toko.index', compact('tokos'));
    }

    // ===== FORM TAMBAH TOKO =====
    public function create()
    {
        return view('toko.create');
    }

    // ===== SIMPAN TOKO BARU =====
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric|min:0',
        ]);

        // Generate barcode unik - format pendek untuk mudah di-scan
        // Format: TOKO + 5 digit ID (padded)
        $lastToko = Toko::latest('id')->first();
        $nextId = ($lastToko ? $lastToko->id : 0) + 1;
        $barcode = 'TOKO' . str_pad($nextId, 5, '0', STR_PAD_LEFT); // Contoh: TOKO00001

        Toko::create([
            'nama_toko' => $request->nama_toko,
            'alamat'    => $request->alamat,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
            'barcode'   => $barcode,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil ditambahkan!');
    }

    // ===== FORM EDIT TOKO =====
    public function edit($id)
    {
        $toko = Toko::findOrFail($id);
        return view('toko.edit', compact('toko'));
    }

    // ===== UPDATE TOKO =====
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric|min:0',
        ]);

        $toko = Toko::findOrFail($id);
        $toko->update([
            'nama_toko' => $request->nama_toko,
            'alamat'    => $request->alamat,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil diperbarui!');
    }

    // ===== HAPUS TOKO =====
    public function destroy($id)
    {
        Toko::findOrFail($id)->delete();
        return back()->with('success', 'Toko berhasil dihapus!');
    }

    // ===== CETAK BARCODE TOKO (PDF) =====
    public function cetakBarcode($id)
    {
        $toko    = Toko::findOrFail($id);
        $barcode = BarcodeService::generateBase64($toko->barcode);
        $pdf     = Pdf::loadView('toko.barcode-pdf', compact('toko', 'barcode'))
                      ->setPaper([0, 0, 200, 100], 'portrait');
        return $pdf->stream('barcode-toko-' . $toko->id . '.pdf');
    }

    // ===== HALAMAN KUNJUNGAN TOKO =====
    public function kunjungan()
    {
        $riwayat = Kunjungan::with('toko')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();
        return view('toko.kunjungan', compact('riwayat'));
    }

    // ===== AJAX: CARI TOKO BERDASARKAN BARCODE HASIL SCAN =====
    public function findByBarcode(Request $request)
    {
        $barcode = trim($request->barcode);
        
        // Debug log
        \Log::info('Barcode scanned', ['barcode' => $barcode, 'length' => strlen($barcode)]);

        // Coba cari exact match
        $toko = Toko::where('barcode', $barcode)->first();

        // Jika tidak found dan barcode pendek, coba search dengan LIKE
        if (!$toko && strlen($barcode) < 15) {
            $toko = Toko::where('barcode', 'LIKE', $barcode . '%')->first();
        }

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan dengan barcode: ' . $barcode,
            ], 404);
        }

        return response()->json([
            'success'   => true,
            'id'        => $toko->id,
            'nama_toko' => $toko->nama_toko,
            'alamat'    => $toko->alamat ?? '-',
            'latitude'  => $toko->latitude,
            'longitude' => $toko->longitude,
            'accuracy'  => $toko->accuracy,
        ]);
    }

    // ===== AJAX: SIMPAN KUNJUNGAN (VALIDASI HAVERSINE) =====
    public function simpanKunjungan(Request $request)
    {
        $request->validate([
            'toko_id'        => 'required|exists:toko,id',
            'latitude_sales' => 'required|numeric',
            'longitude_sales'=> 'required|numeric',
            'accuracy_sales' => 'required|numeric|min:0',
        ]);

        $toko = Toko::findOrFail($request->toko_id);

        // Hitung jarak dengan formula Haversine
        $jarak = $this->haversine(
            $toko->latitude,  $toko->longitude,
            $request->latitude_sales, $request->longitude_sales
        );

        // Threshold efektif = radius + accuracy toko + accuracy sales
        $radiusThreshold   = 300; // meter, bisa dikonfigurasi
        $thresholdEfektif  = $radiusThreshold + $toko->accuracy + $request->accuracy_sales;

        // Tentukan status
        $status = $jarak <= $thresholdEfektif ? 'diterima' : 'ditolak';

        // Simpan ke database
        $kunjungan = Kunjungan::create([
            'toko_id'          => $toko->id,
            'user_id'          => Auth::id(),
            'latitude_sales'   => $request->latitude_sales,
            'longitude_sales'  => $request->longitude_sales,
            'accuracy_sales'   => $request->accuracy_sales,
            'jarak_meter'      => round($jarak, 2),
            'threshold_efektif'=> round($thresholdEfektif, 2),
            'status'           => $status,
            'radius_threshold' => $radiusThreshold,
        ]);

        return response()->json([
            'success'           => true,
            'status'            => $status,
            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($thresholdEfektif, 2),
            'radius_threshold'  => $radiusThreshold,
            'accuracy_toko'     => $toko->accuracy,
            'accuracy_sales'    => $request->accuracy_sales,
            'nama_toko'         => $toko->nama_toko,
            'pesan'             => $status === 'diterima'
                ? 'Kunjungan DITERIMA. Anda berada dalam radius toko.'
                : 'Kunjungan DITOLAK. Anda berada di luar radius toko.',
        ]);
    }

    // ===== FORMULA HAVERSINE =====
    // Menghitung jarak antara dua koordinat dalam meter
    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R    = 6371000; // radius bumi dalam meter
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLng = ($lng2 - $lng1) * M_PI / 180;

        $a = sin($dLat / 2) ** 2
            + cos($lat1 * M_PI / 180)
            * cos($lat2 * M_PI / 180)
            * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c; // jarak dalam meter
    }
}
