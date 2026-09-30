<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            @if($orders->count() > 0)
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b text-left text-gray-600 bg-gray-50">
                            <th class="p-3">No. Transaksi</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Total Bayar</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b">
                                <td class="p-3 font-semibold">{{ $order->order_number }}</td>
                                <td class="p-3">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="p-3 font-bold text-amber-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="p-3">
                                    @if($order->status == 'paid')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">LUNAS</span>
                                    @elseif($order->status == 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">MENUNGGU PEMBAYARAN</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">GAGAL/BATAL</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center space-x-2">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 font-semibold hover:underline">Detail</a>
                                    @if($order->status == 'pending')
                                        <a href="{{ route('checkout.payment', $order->id) }}" class="bg-amber-600 text-white text-xs px-3 py-1 rounded hover:bg-amber-700">Bayar</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>Anda belum memiliki riwayat transaksi.</p>
                    <a href="{{ route('shop.index') }}" class="mt-4 inline-block bg-amber-600 text-white px-6 py-2 rounded-md font-semibold">Mulai Belanja</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
