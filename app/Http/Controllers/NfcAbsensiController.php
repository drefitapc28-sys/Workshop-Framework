<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\KartuNfc;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class NfcAbsensiController extends Controller
{
    // Scanner NFC

    /* Halaman Scanner NFC utama */
    public function scanner()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('nfc.scanner', compact('mahasiswas'));
    }

    /* API; proses scan NFC - cek kartu & cek absensi */
    public function prosesScan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'mata_kuliah' => 'required|string',
            'isi_kartu' => 'nullable|string',
        ]);

        $serial = strtoupper(trim($request->serial_number));
       
        // Cari kartu NFC berdasarkan serial number
        $kartu = KartuNfc::with('mahasiswa')
            ->where('serial_number', $serial)
            ->where('aktif', true)
            ->first();

        // Kartu tidak terdaftar
        if (!$kartu) {
            return response()->json([
                'status' => 'tidak_terdaftar',
                'pesan' => 'Kartu NFC tidak terdaftar atau tidak aktif.',
            ], 404);
        }

        $mahasiswa = $kartu->mahasiswa;

        // Cek apakah sudah absensi hari ini untuk mata kuliah ini
        $sudahAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('waktu_scan', today())
            ->where($request->mata_kuliah, fn($q) => $q->where('mata_kuliah', $request->mata_kuliah))
            ->exists();

         if ($sudahAbsen) {
            return response()->json([
                'status'     => 'sudah_absen',
                'pesan'      => 'Mahasiswa ini sudah absen hari ini.',
                'mahasiswa'  => [
                    'nama'  => $mahasiswa->nama,
                    'nim'   => $mahasiswa->nim,
                    'prodi' => $mahasiswa->prodi,
                ],
            ], 200);
        }
 
        // Tentukan status: hadir jika sebelum jam 08:30, terlambat jika sesudahnya
        $jamSekarang = now();
        $batasHadir  = now()->setTime(8, 30, 0);
        $statusAbsen = $jamSekarang->lte($batasHadir) ? 'hadir' : 'terlambat';
 
        // Simpan absensi
        $absensi = Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kartu_nfc_id' => $kartu->id,
            'serial_number'=> $serial,
            'mata_kuliah'  => $request->mata_kuliah ?? 'Umum',
            'status'       => $statusAbsen,
            'waktu_scan'   => $jamSekarang,
            'keterangan'   => $request->isi_kartu ?? null,
        ]);
 
        return response()->json([
            'status'    => 'berhasil',
            'pesan'     => 'Absensi berhasil dicatat.',
            'absensi'   => [
                'id'          => $absensi->id,
                'status'      => $statusAbsen,
                'waktu'       => $jamSekarang->format('H:i:s'),
                'mata_kuliah' => $absensi->mata_kuliah,
            ],
            'mahasiswa' => [
                'nama'  => $mahasiswa->nama,
                'nim'   => $mahasiswa->nim,
                'prodi' => $mahasiswa->prodi,
                'kelas' => $mahasiswa->kelas,
            ],
        ], 201);
    }
 
    // MANAJEMEN KARTU NFC 
    /* Halaman daftar kartu NFC & form daftarkan kartu baru */
    public function daftarKartu()
    {
        $kartuList  = KartuNfc::with('mahasiswa')->latest()->get();
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('nfc.daftar-kartu', compact('kartuList', 'mahasiswas'));
    }
 
    /* Simpan kartu NFC baru */
    public function simpanKartu(Request $request)
    {
        $request->validate([
            'mahasiswa_id'  => 'required|exists:mahasiswas,id',
            'serial_number' => 'required|string|unique:kartu_nfcs,serial_number',
            'label'         => 'nullable|string|max:100',
        ]);
 
        KartuNfc::create([
            'mahasiswa_id'  => $request->mahasiswa_id,
            'serial_number' => strtoupper(trim($request->serial_number)),
            'label'         => $request->label,
            'aktif'         => true,
        ]);
 
        return response()->json(['message' => 'Kartu berhasil didaftarkan']);
    }
 
    /* Hapus kartu NFC */
    public function hapusKartu($id)
    {
        $kartu = KartuNfc::findOrFail($id);
        $kartu->delete();
        return response()->json(['message' => 'Kartu berhasil dihapus']);
    }
 
    /* Toggle aktif/nonaktif kartu */
    public function toggleKartu($id)
    {
        $kartu = KartuNfc::findOrFail($id);
        $kartu->update(['aktif' => !$kartu->aktif]);
        return response()->json([
            'message' => 'Status kartu diperbarui',
            'aktif'   => $kartu->aktif,
        ]);
    }
 
    // MANAJEMEN MAHASISWA 
    /* Simpan mahasiswa baru */
    public function simpanMahasiswa(Request $request)
    {
        $request->validate([
            'nim'   => 'required|string|unique:mahasiswas,nim',
            'nama'  => 'required|string|max:100',
            'prodi' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:20',
        ]);
 
        $mahasiswa = Mahasiswa::create($request->only('nim', 'nama', 'prodi', 'kelas'));
        return response()->json(['message' => 'Mahasiswa berhasil ditambahkan', 'mahasiswa' => $mahasiswa]);
    }
 
    // RIWAYAT ABSENSI
    /* Halaman riwayat absensi */
    public function riwayat(Request $request)
    {
        $query = Absensi::with(['mahasiswa', 'kartuNfc'])
            ->orderByDesc('waktu_scan');
 
        // Filter tanggal
        if ($request->tanggal) {
            $query->whereDate('waktu_scan', $request->tanggal);
        } else {
            $query->whereDate('waktu_scan', today());
        }
 
        // Filter mata kuliah
        if ($request->mata_kuliah) {
            $query->where('mata_kuliah', $request->mata_kuliah);
        }
 
        $absensis    = $query->get();
        $mataKuliahs = Absensi::distinct()->pluck('mata_kuliah')->filter()->values();
 
        $stats = [
            'hadir'     => $absensis->where('status', 'hadir')->count(),
            'terlambat' => $absensis->where('status', 'terlambat')->count(),
            'total'     => $absensis->count(),
        ];
 
        return view('nfc.riwayat', compact('absensis', 'mataKuliahs', 'stats'));
    }
 
    /* API: Hapus absensi */
    public function hapusAbsensi($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();
        return response()->json(['message' => 'Absensi berhasil dihapus']);
    }
}