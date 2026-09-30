<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Transaksi: ') . $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <div class="flex justify-between items-center border-b pb-4">
                <div>
                    <p class="text-sm text-gray-500">Tanggal Transaksi</p>
                    <p class="font-semibold">{{ $order->created_at->format('d MMMM Y, H:i') }}</p>
                </div>
                <div>
                    <span class="text-sm font-semibold">Status: </span>
                    <span class="font-bold uppercase {{ $order->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 mb-2">Informasi Pengiriman</h3>
                <p class="text-gray-600"><strong>No. HP:</strong> {{ $order->phone }}</p>
                <p class="text-gray-600"><strong>Alamat:</strong> {{ $order->address }}</p>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                <div class="flex justify-between text-xl font-extrabold text-amber-700">
                    <span>Total Tagihan:</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="border-t pt-4 flex justify-between">
                <a href="{{ route('orders.index') }}" class="text-gray-600 font-semibold hover:underline">&larr; Kembali ke Riwayat</a>
                @if($order->status == 'pending')
                    <a href="{{ route('checkout.payment', $order->id) }}" class="bg-amber-600 text-white px-6 py-2 rounded-md font-bold hover:bg-amber-700">Bayar Sekarang</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
