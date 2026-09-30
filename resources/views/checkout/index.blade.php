<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pengiriman & Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block font-semibold text-gray-700">Nomor WhatsApp / Telepon</label>
                    <input type="text" name="phone" required placeholder="081234567890" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700">Alamat Lengkap Pengiriman</label>
                    <textarea name="address" rows="3" required placeholder="Nama Jalan, No. Rumah, Kecamatan, Kota" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="border-t pt-4">
                    <h3 class="font-bold text-lg mb-2">Ringkasan Pesanan</h3>
                    <ul class="divide-y">
                        @foreach($cart as $item)
                            <li class="py-2 flex justify-between">
                                <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                                <span class="font-semibold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="flex justify-between font-extrabold text-xl mt-4 text-amber-700">
                        <span>Total Bayar:</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-md font-bold text-lg hover:bg-green-700 transition">
                    Lanjut ke Pembayaran &rarr;
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
