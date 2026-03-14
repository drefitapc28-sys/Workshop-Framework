<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /* Tampilkan halaman utama Wilayah */
    public function index()
    {
        return view('wilayah.index');
    }

    /* GET semua Provinsi
       Endpoint: GET /wilayah/provinsi
    */
    public function getProvinsi()
    {
        $data = DB::table('reg_provinces')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data provinsi berhasil diambil',
            'data'    => $data
        ]);
    }

    /* GET Kota/Kabupaten berdasarkan Province ID
       Endpoint: GET /wilayah/kota/{province_id}
    */
    public function getKota($province_id)
    {
        $data = DB::table('reg_regencies')
            ->where('province_id', $province_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kota berhasil diambil',
            'data'    => $data
        ]);
    }

    /* GET Kecamatan berdasarkan Regency ID
       Endpoint: GET /wilayah/kecamatan/{regency_id}
    */
    public function getKecamatan($regency_id)
    {
        $data = DB::table('reg_districts')
            ->where('regency_id', $regency_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kecamatan berhasil diambil',
            'data'    => $data
        ]);
    }

    /* GET Kelurahan berdasarkan District ID
       Endpoint: GET /wilayah/kelurahan/{district_id}
    */
    public function getKelurahan($district_id)
    {
        $data = DB::table('reg_villages')
            ->where('district_id', $district_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kelurahan berhasil diambil',
            'data'    => $data
        ]);
    }
}