<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    // 2. Proses Checkout & Simpan Order ke Database
    public function process(Request $request)
    {
        $request->validate([
            'fulfillment_type' => 'required|in:pickup,delivery',
            'address'          => 'nullable|required_if:fulfillment_type,delivery|string',
            'note'             => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Keranjang belanja kosong.');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemDetails = [];

            foreach ($cart as $id => $details) {
                $subtotal += $details['price'] * $details['quantity'];
                $itemDetails[] = [
                    'id'       => (string) $id,
                    'price'    => (int) $details['price'],
                    'quantity' => (int) $details['quantity'],
                    'name'     => substr($details['name'], 0, 50),
                ];
            }

            $shippingFee = $request->fulfillment_type === 'delivery' ? 10000 : 0;
            $total = $subtotal + $shippingFee;

            if ($shippingFee > 0) {
                $itemDetails[] = [
                    'id'       => 'SHIPPING',
                    'price'    => (int) $shippingFee,
                    'quantity' => 1,
                    'name'     => 'Ongkos Kirim',
                ];
            }

            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            // Menggabungkan Alamat dan Catatan
            $fullNote = $request->note;
            if ($request->fulfillment_type === 'delivery' && $request->address) {
                $fullNote = 'Alamat Pengiriman: ' . $request->address . ($request->note ? ' | Catatan: ' . $request->note : '');
            }

            // Simpan ke Tabel Orders
            $order = Order::create([
                'user_id'          => auth()->id(),
                'invoice_no'       => $invoiceNo,
                'fulfillment_type' => $request->fulfillment_type,
                'subtotal'         => $subtotal,
                'shipping_fee'     => $shippingFee,
                'discount'         => 0,
                'total'            => $total,
                'status'           => 'pending',
                'note'             => $fullNote,
            ]);

            // Simpan ke Tabel OrderItems
            foreach ($cart as $productId => $details) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $details['quantity'],
                    'price'      => $details['price'],
                ]);
            }

            // Midtrans Token
            $this->initMidtrans();

            $params = [
                'transaction_details' => [
                    'order_id'     => $order->invoice_no,
                    'gross_amount' => (int) $order->total,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email'      => auth()->user()->email,
                    'phone'      => auth()->user()->phone ?? '08123456789',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Simpan ke Tabel Payments
            Payment::create([
                'order_id'    => $order->id,
                'method'      => 'midtrans',
                'amount'      => $order->total,
                'status'      => 'pending',
                'gateway_ref' => $snapToken,
            ]);

            DB::commit();

            session()->forget('cart');

            return redirect()->route('checkout.payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // 3. Tampilkan Halaman Pembayaran Snap Midtrans
    public function payment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment']);

        return view('checkout.payment', compact('order'));
    }

    // 4. Webhook Callback Otomatis dari Midtrans
    public function callback(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();
            $transactionStatus = $notif->transaction_status;
            $invoiceNo = $notif->order_id;
            $fraudStatus = $notif->fraud_status;

            $order = Order::where('invoice_no', $invoiceNo)->first();

            if (!$order) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            $payment = Payment::where('order_id', $order->id)->first();

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $order->update(['status' => 'paid']);
                    $payment?->update(['status' => 'settlement', 'paid_at' => now()]);
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update(['status' => 'paid']);
                $payment?->update(['status' => 'settlement', 'paid_at' => now()]);
            } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $order->update(['status' => 'cancelled']);
                $payment?->update(['status' => $transactionStatus]);
            }

            return response()->json(['message' => 'Notifikasi berhasil diproses']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
