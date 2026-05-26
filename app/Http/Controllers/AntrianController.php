<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    private array $daftarPoli = [
        'Umum',
        'Poli Jantung',
        'Poli Gigi',
        'Poli Penyakit Dalam',
        'Poli Mata',
        'Poli THT',
        'Poli Anak',
        'Poli Kandungan',
    ];

    // GUEST (Public)
    public function guestForm()
    {
        return view('guest.form', [
            'daftarPoli' => $this->daftarPoli,
        ]);
    }

    public function guestStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'poli' => 'required|string',
        ]);

        $antrian = Antrian::create([
            'nomor_antrian' => Antrian::nomorBerikutnya(),
            'nama'          => $request->nama,
            'poli'          => $request->poli,
            'status'        => 'menunggu',
            'jam_daftar'    => now(),
        ]);

        return view('guest.tiket', compact('antrian'));
    }

    // ADMIN (Auth)
    public function adminDashboard()
    {
        // Optimize: 1 query instead of 6
        $antrians = Antrian::hariIni()->orderBy('nomor_antrian')->get();
        
        $dipanggil = $antrians->firstWhere('status', 'dipanggil');
        
        $stats = [
            'menunggu'  => $antrians->where('status', 'menunggu')->count(),
            'dipanggil' => $antrians->where('status', 'dipanggil')->count(),
            'terlambat' => $antrians->where('status', 'terlambat')->count(),
            'selesai'   => $antrians->where('status', 'selesai')->count(),
        ];

        return view('admin.antrian', compact('antrians', 'dipanggil', 'stats'));
    }

    public function panggilBerikutnya()
    {
        // Tandai yang dipanggil → selesai
        Antrian::hariIni()->where('status', 'dipanggil')->update(['status' => 'selesai']);

        // Ambil yang menunggu berikutnya
        $next = Antrian::hariIni()
            ->where('status', 'menunggu')
            ->orderBy('nomor_antrian')
            ->first();

        if (!$next) {
            return response()->json(['message' => 'Tidak ada antrian yang menunggu'], 404);
        }

        $next->update(['status' => 'dipanggil']);

        return response()->json(['message' => 'Berhasil memanggil', 'antrian' => $next]);
    }

    public function tandaiTerlambat(Request $request)
    {
        $request->validate(['id' => 'required|exists:antrians,id']);

        $antrian = Antrian::findOrFail($request->id);
        $antrian->update(['status' => 'terlambat']);

        return response()->json(['message' => 'Ditandai terlambat', 'antrian' => $antrian]);
    }

    public function panggilTerlambat(Request $request)
    {
        $request->validate(['id' => 'required|exists:antrians,id']);

        // Tandai yang sedang dipanggil → selesai
        Antrian::hariIni()->where('status', 'dipanggil')->update(['status' => 'selesai']);

        $antrian = Antrian::findOrFail($request->id);
        $antrian->update(['status' => 'dipanggil']);

        return response()->json(['message' => 'Berhasil memanggil ulang', 'antrian' => $antrian]);
    }

    public function resetAntrian()
    {
        Antrian::hariIni()->delete();
        return response()->json(['message' => 'Antrian hari ini telah direset']);
    }

    public function tandaiSelesai(Request $request)
    {
        $request->validate(['id' => 'required|exists:antrians,id']);

        $antrian = Antrian::findOrFail($request->id);
        $antrian->update(['status' => 'selesai']);

        return response()->json(['message' => 'Ditandai selesai', 'antrian' => $antrian]);
    }


    // PAPAN ANTRIAN (Public)
    public function papanAntrian()
    {
        return view('papan.index');
    }

    // SSE STREAM — inti modul 10
    public function stream(Request $request)
    {
        session()->save();

        return response()->stream(function () {
            set_time_limit(0);
            $counter = 0;

            while (true) {
                // Cek apakah client masih terhubung
                if (connection_aborted()) break;

                // Reconnect DB jika koneksi drop setelah sleep panjang
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    DB::reconnect();
                }

                // Optimize: 1 query instead of 6
                $semua = Antrian::hariIni()
                    ->orderBy('nomor_antrian')
                    ->get(['id', 'nomor_antrian', 'nama', 'poli', 'status', 'jam_daftar']);

                $dipanggil = $semua->firstWhere('status', 'dipanggil');

                $stats = [
                    'menunggu'  => $semua->where('status', 'menunggu')->count(),
                    'dipanggil' => $semua->where('status', 'dipanggil')->count(),
                    'terlambat' => $semua->where('status', 'terlambat')->count(),
                    'selesai'   => $semua->where('status', 'selesai')->count(),
                ];

                $payload = [
                    'dipanggil' => $dipanggil,
                    'menunggu'  => $semua->where('status', 'menunggu')->values(),
                    'semua'     => $semua,
                    'stats'     => $stats,
                    'timestamp' => now()->format('H:i:s'),
                ];

                echo 'event: queue-update' . PHP_EOL;
                echo 'data: ' . json_encode($payload) . PHP_EOL;
                echo PHP_EOL;

                ob_flush();
                flush();

                // Keep-alive comment every 5 updates to prevent timeout
                $counter++;
                if ($counter % 5 === 0) {
                    echo ': keep-alive' . PHP_EOL . PHP_EOL;
                    ob_flush();
                    flush();
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
            'Pragma'            => 'no-cache',
            'Expires'           => '0',
        ]);
    }
}