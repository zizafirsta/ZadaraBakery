<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    private function initMidtrans()
    {
        Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // 1. Tampilkan Halaman Form Checkout
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'total'));
    }

    // 2. Proses Checkout & Dapatkan Snap Token Midtrans
    public function process(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'phone'   => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index');
        }

        $totalPrice = 0;
        $itemDetails = [];
        foreach ($cart as $id => $details) {
            $totalPrice += $details['price'] * $details['quantity'];
            $itemDetails[] = [
                'id'       => $id,
                'price'    => $details['price'],
                'quantity' => $details['quantity'],
                'name'     => substr($details['name'], 0, 50),
            ];
        }

        // Buat ID Transaksi Unik
        $orderNumber = 'TRX-' . time() . '-' . rand(100, 999);

        // Simpan Order ke Database
        $order = Order::create([
            'user_id'      => auth()->id(),
            'order_number' => $orderNumber,
            'total_price'  => $totalPrice,
            'status'       => 'pending',
            'address'      => $request->address,
            'phone'        => $request->phone,
        ]);

        // Inisialisasi Param Midtrans
        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => $order->total_price,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email'      => auth()->user()->email,
                'phone'      => $request->phone,
            ],
        ];

        // Minta Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);
        $order->update(['snap_token' => $snapToken]);

        // Kosongkan keranjang belanja
        session()->forget('cart');

        return redirect()->route('checkout.payment', $order->id);
    }

    // 3. Tampilkan Halaman Pembayaran Snap
    public function payment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.payment', compact('order'));
    }

    // 4. Webhook Callback Otomatis dari Midtrans (Sistem Otomatis Mengubah Status)
    public function callback(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();
            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status;

            $order = Order::where('order_number', $orderId)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['status' => 'pending']);
                } else if ($fraudStatus == 'accept') {
                    $order->update(['status' => 'paid']);
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update(['status' => 'paid']);
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $order->update(['status' => 'failed']);
            } else if ($transactionStatus == 'pending') {
                $order->update(['status' => 'pending']);
            }

            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
