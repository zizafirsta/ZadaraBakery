<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Tampilkan seluruh transaksi pelanggan
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Tampilkan detail transaksi
    public function show(Order $order)
    {
        $order->load('user');
        return view('admin.orders.show', compact('order'));
    }

    // Update status transaksi secara manual jika diperlukan (misal: paid, cancelled)
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,failed',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui!');
    }
}
