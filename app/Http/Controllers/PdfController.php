<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function generateSertifikat()
    {
        Carbon::setLocale('id');
        
        $data = [
            'nama' => auth()->user()->name, //dinamis sesuai user yang login
            'peran' => 'Peserta',
            'kegiatan' => 'Workshop Laravel & HTML to PDF Generator',
            'penyelenggara' => 'Program Studi Teknik Informatika',
            'institusi' => 'Universitas Airlangga',
            'tanggal' => Carbon::now()->translatedFormat('d F Y'), //dinamis sesuai tanggal saat generate
            'nomor_sertifikat' => '001/SERTIF/'.date('Y'), //dinamis sesuai tahun
        ];
        //key array otomatis menjadi variabel di view, jadi di view bisa langsung panggil $nama, $peran, dll

        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat.pdf');
    }

    public function generateUndangan()
    {
        Carbon::setLocale('id');
        
        $data = [
            'nomor' => 'UN/FV/' . date('Y') . '/001',
            'perihal' => 'Undangan Rapat Koordinasi',
            'kepada' => 'Bapak/Ibu Dosen Fakultas Vokasi Universitas Airlangga',
            'hari_tanggal' => Carbon::now()->translatedFormat('l, d F Y'),
            'waktu' => '09:00 WIB - Selesai',
            'tempat' => 'Ruang Rapat Lt. 3 Gedung F Fakultas Vokasi Universitas Airlangga',
            'kota_tanggal' => 'Surabaya, ' . Carbon::now()->translatedFormat('d F Y'),
            'dekan' => 'Prof. Dian Yulie Reindrawati S.Sos., MM., Ph.D.',
            'nip' => '198501012010011001',
        ];

        $pdf = Pdf::loadView('pdf.undangan', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }

}
