<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // Halaman pemesanan utama
    public function index()
    {
        $vendors = Vendor::all();
        return view('customer.order', compact('vendors'));
    }

    // AJAX: ambil menu berdasarkan vendor
    public function getMenuByVendor($idvendor)
    {
        $menus = Menu::where('idvendor', $idvendor)->get();
        return response()->json($menus);
    }

    // Proses simpan pesanan & panggil Midtrans
    public function store(Request $request)
    {
        $request->validate([
            'idvendor'        => 'required|exists:vendor,idvendor',
            'items'           => 'required|array|min:1',
            'items.*.idmenu'  => 'required|exists:menu,idmenu',
            'items.*.jumlah'  => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Generate nama guest otomatis
            $lastPesanan = Pesanan::latest('idpesanan')->first();
            $nextNumber  = $lastPesanan ? ($lastPesanan->idpesanan + 1) : 1;
            $namaGuest   = 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

            // Hitung total
            $total     = 0;
            $itemsData = [];
            foreach ($request->items as $item) {
                $menu     = Menu::findOrFail($item['idmenu']);
                $subtotal = $menu->harga * $item['jumlah'];
                $total   += $subtotal;
                $itemsData[] = [
                    'menu'     => $menu,
                    'jumlah'   => $item['jumlah'],
                    'subtotal' => $subtotal,
                    'catatan'  => $item['catatan'] ?? null,
                ];
            }

            // Buat order_id unik untuk Midtrans
            $orderId = 'ORDER-' . time() . '-' . rand(100, 999);

            // Simpan pesanan
            $pesanan = Pesanan::create([
                'nama'              => $namaGuest,
                'total'             => $total,
                'metode_bayar'      => 'virtual_account', // default, diupdate saat callback
                'status_bayar'      => 'pending',
                'midtrans_order_id' => $orderId,
                'idvendor'          => $request->idvendor,
            ]);

            // Simpan detail pesanan
            foreach ($itemsData as $item) {
                DetailPesanan::create([
                    'idmenu'    => $item['menu']->idmenu,
                    'idpesanan' => $pesanan->idpesanan,
                    'jumlah'    => $item['jumlah'],
                    'harga'     => $item['menu']->harga,
                    'subtotal'  => $item['subtotal'],
                    'catatan'   => $item['catatan'],
                ]);
            }

            // Setup Midtrans
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $namaGuest,
                ],
                'item_details' => array_map(function ($item) {
                    return [
                        'id'       => $item['menu']->idmenu,
                        'price'    => $item['menu']->harga,
                        'quantity' => $item['jumlah'],
                        'name'     => $item['menu']->nama_menu,
                    ];
                }, $itemsData),
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $pesanan->midtrans_token = $snapToken;
            $pesanan->save();

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
                'idpesanan'  => $pesanan->idpesanan,
                'nama'       => $namaGuest,
                'total'      => $total,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Customer Order Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Callback dari Midtrans (notification URL / webhook)
    public function midtransCallback(Request $request)
    {
        try {
            // Get raw request data (Midtrans sends form data, not JSON)
            $data = $request->all();

            // Extract dari payload Midtrans (tanpa verify ke API)
            $orderId           = $data['order_id'] ?? null;
            $transactionId     = $data['transaction_id'] ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;
            $paymentType       = $data['payment_type'] ?? null;
            $fraudStatus       = $data['fraud_status'] ?? 'accept';

            // Debug logging
            \Log::info('Midtrans Webhook Received', [
                'order_id'            => $orderId,
                'transaction_id'      => $transactionId,
                'transaction_status'  => $transactionStatus,
                'payment_type'        => $paymentType,
                'fraud_status'        => $fraudStatus,
            ]);

            // Validasi order_id
            if (!$orderId) {
                \Log::error('Midtrans Webhook Error: order_id is null', [
                    'raw_request' => $data,
                ]);
                return response()->json(['message' => 'Invalid order_id'], 200); // Return 200 ke Midtrans
            }

            // Cari pesanan
            $pesanan = Pesanan::where('midtrans_order_id', $orderId)->first();
            if (!$pesanan) {
                \Log::warning('Midtrans Webhook: Order not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'OK']); // Return 200 tetap (supaya Midtrans tidak retry)
            }

            // Update status bayar berdasarkan transaction_status
            if ($transactionStatus == 'capture') {
                // Credit card payment captured
                if ($fraudStatus == 'accept') {
                    $pesanan->status_bayar = 'lunas';
                } else {
                    $pesanan->status_bayar = 'pending'; // Fraud detected
                }
            } elseif ($transactionStatus == 'settlement') {
                // Payment settled
                $pesanan->status_bayar = 'lunas';
            } elseif ($transactionStatus == 'pending') {
                // Waiting for payment
                $pesanan->status_bayar = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // Payment failed/expired
                $pesanan->status_bayar = 'pending';
            }

            // Update metode bayar
            if ($paymentType == 'bank_transfer' || $paymentType == 'echannel') {
                $pesanan->metode_bayar = 'virtual_account';
            } elseif (in_array($paymentType, ['qris', 'gopay', 'shopeepay'])) {
                $pesanan->metode_bayar = 'qris';
            } elseif ($paymentType == 'credit_card') {
                $pesanan->metode_bayar = 'kartu_kredit';
            }

            // Simpan transaction ID
            if ($transactionId) {
                $pesanan->midtrans_transaction_id = $transactionId;
            }

            $pesanan->save();

            \Log::info('Midtrans Webhook Success', [
                'order_id'  => $orderId,
                'status'    => $pesanan->status_bayar,
                'metode'    => $pesanan->metode_bayar,
            ]);

            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            \Log::error('Midtrans Webhook Exception: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'request' => $request->all(),
            ]);
            // Return 200 tetap, jangan return 500 (Midtrans akan retry terus)
            return response()->json(['message' => 'OK'], 200);
        }
    }

    // Halaman status pembayaran
    public function paymentStatus($orderId)
    {
        $pesanan = Pesanan::with(['detailPesanans.menu', 'vendor'])
            ->where('midtrans_order_id', $orderId)
            ->firstOrFail();
        return view('customer.payment_status', compact('pesanan'));
    }

    // Finish redirect dari Midtrans
    public function finishPayment(Request $request)
    {
        $orderId = $request->order_id;
        $pesanan = Pesanan::where('midtrans_order_id', $orderId)->firstOrFail();

        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $status = \Midtrans\Transaction::status($orderId);
            if (in_array($status->transaction_status, ['settlement', 'capture'])) {
                $pesanan->status_bayar = 'lunas';
                $pesanan->save();
            }
        } catch (\Exception $e) {
            // Tetap lanjut ke halaman status
        }

        return redirect()->route('customer.payment.status', $orderId);
    }
}