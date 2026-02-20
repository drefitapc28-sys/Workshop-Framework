<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generateSertifikat()
    {
        $data = [
            'nama' => auth()->user()->name,
            'peran' => 'Peserta',
            'kegiatan' => 'Workshop Laravel & HTML to PDF Generator',
            'penyelenggara' => 'Program Studi Teknik Informatika',
            'institusi' => 'Universitas Airlangga',
            'tanggal' => date('d F Y'),
            'nomor_sertifikat' => '001/SERTIF/'.date('Y'),
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', $data);

        return $pdf->download('sertifikat.pdf');
    }

    public function generateUndangan()
    {
        $data = [
            'nomor' => 'UN/FV/' . date('Y') . '/001',
            'perihal' => 'Undangan Rapat Koordinasi',
            'kepada' => 'Bapak/Ibu Dosen Fakultas Vokasi Universitas Airlangga',
            'hari_tanggal' => date('l, d F Y'),
            'waktu' => '09:00 WIB - Selesai',
            'tempat' => 'Ruang Rapat Lt. 3 Gedung F Fakultas Vokasi Universitas Airlangga',
            'kota_tanggal' => 'Surabaya, ' . date('d F Y'),
            'dekan' => 'Prof. Dian Yulie Reindrawati S.Sos., MM., Ph.D.',
            'nip' => '198501012010011001',
        ];

        $pdf = Pdf::loadView('pdf.undangan', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }

}
