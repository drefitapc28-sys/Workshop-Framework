<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerManagementController extends Controller
{
    /**
     * Dashboard/Index Customer Management
     */
    public function index()
    {
        $totalCustomers = Customer::count();
        $totalBlob = Customer::where('tipe_foto', 'blob')->count();
        $totalFile = Customer::where('tipe_foto', 'file')->count();

        return view('customer.management.index', compact('totalCustomers', 'totalBlob', 'totalFile'));
    }

    /**
     * Tampilkan Tabel Data Customer
     */
    public function data()
    {
        $customers = Customer::all();
        return view('customer.management.data', compact('customers'));
    }

    /**
     * Form Tambah Customer 1 (Blob Storage)
     */
    public function create1()
    {
        return view('customer.management.tambah-1');
    }

    /**
     * Store Tambah Customer 1 (Blob Storage)
     * Foto disimpan sebagai base64 string di database
     */
    public function store1(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama'               => 'required|string|max:255',
                'alamat'             => 'required|string',
                'provinsi'           => 'required|string|max:100',
                'kota'               => 'required|string|max:100',
                'kecamatan'          => 'required|string|max:100',
                'kodepos_keluarahan' => 'required|string|max:10',
                'foto_blob'          => 'required|string',
            ], [
                'nama.required'     => 'Nama harus diisi',
                'foto_blob.required'=> 'Foto tidak boleh kosong. Silakan ambil foto terlebih dahulu.',
            ]);

            $base64String = $validated['foto_blob'];

            // Hapus data URL header jika ada (contoh: data:image/png;base64,)
            if (strpos($base64String, ',') !== false) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            }

            // Bersihkan whitespace dan karakter tidak valid
            $base64String = preg_replace('/\s+/', '', $base64String);

            // Validasi apakah base64 valid
            if (base64_decode($base64String, true) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Base64 decoding gagal. Foto mungkin tidak valid.',
                ], 400);
            }

            // Simpan ke database sebagai base64 string lengkap dengan header
            $customer = Customer::create([
                'nama'               => $validated['nama'],
                'alamat'             => $validated['alamat'],
                'provinsi'           => $validated['provinsi'],
                'kota'               => $validated['kota'],
                'kecamatan'          => $validated['kecamatan'],
                'kodepos_keluarahan' => $validated['kodepos_keluarahan'],
                'foto'               => 'data:image/png;base64,' . $base64String,
                'tipe_foto'          => 'blob',
            ]);

            \Log::info('Customer BLOB created', ['id' => $customer->id]);

            return response()->json([
                'success'  => true,
                'message'  => 'Customer berhasil ditambah (Blob Storage)',
                'redirect' => route('customer.management.data'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation Error Store1:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()[array_key_first($e->errors())]),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Customer Store1 Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Form Tambah Customer 2 (File Storage)
     */
    public function create2()
    {
        return view('customer.management.tambah-2');
    }

    /**
     * Store Tambah Customer 2 (File Storage)
     * Foto disimpan sebagai file di storage/app/public/customers/
     * dan path-nya disimpan di database
     */
    public function store2(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama'               => 'required|string|max:255',
                'alamat'             => 'required|string',
                'provinsi'           => 'required|string|max:100',
                'kota'               => 'required|string|max:100',
                'kecamatan'          => 'required|string|max:100',
                'kodepos_keluarahan' => 'required|string|max:10',
                'foto_file'          => 'required|string',
            ], [
                'nama.required'      => 'Nama harus diisi',
                'foto_file.required' => 'Foto tidak boleh kosong. Silakan ambil foto terlebih dahulu.',
            ]);

            $base64String = $validated['foto_file'];

            // Hapus data URL header jika ada
            if (strpos($base64String, ',') !== false) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            }

            // Decode base64 menjadi binary image
            $imageData = base64_decode($base64String, true);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Base64 decoding gagal. Foto mungkin tidak valid.',
                ], 400);
            }

            // Generate nama file unik
            $filename = 'customer_' . time() . '_' . uniqid() . '.png';
            $path     = 'customers/' . $filename;

            // Simpan file ke storage public
            $saved = Storage::disk('public')->put($path, $imageData);

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: Gagal menyimpan file ke storage.',
                ], 500);
            }

            // Simpan path ke database
            $customer = Customer::create([
                'nama'               => $validated['nama'],
                'alamat'             => $validated['alamat'],
                'provinsi'           => $validated['provinsi'],
                'kota'               => $validated['kota'],
                'kecamatan'          => $validated['kecamatan'],
                'kodepos_keluarahan' => $validated['kodepos_keluarahan'],
                'foto_path'          => $path,
                'tipe_foto'          => 'file',
            ]);

            \Log::info('Customer FILE created', ['id' => $customer->id, 'path' => $path]);

            return response()->json([
                'success'  => true,
                'message'  => 'Customer berhasil ditambah (File Storage)',
                'redirect' => route('customer.management.data'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation Error Store2:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()[array_key_first($e->errors())]),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Customer Store2 Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus Customer
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Hapus file dari storage jika tipe foto adalah file
            if ($customer->tipe_foto === 'file' && $customer->foto_path) {
                Storage::disk('public')->delete($customer->foto_path);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Customer Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customer: ' . $e->getMessage(),
            ], 500);
        }
    }
}