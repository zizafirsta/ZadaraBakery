<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pengiriman & Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">

            <!-- Menampilkan Pesan Error Validasi Jika Ada -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Pilihan Tipe Pengiriman (Fulfillment Type) -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Metode Pengambilan/Pengiriman</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="border p-4 rounded-lg flex items-center space-x-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="fulfillment_type" value="delivery" id="type_delivery" checked onchange="toggleDeliveryOptions()" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="font-bold block text-gray-800">Dikirim (Delivery)</span>
                                <span class="text-xs text-gray-500">Biaya Ongkir Flat Rp 10.000</span>
                            </div>
                        </label>
                        <label class="border p-4 rounded-lg flex items-center space-x-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="fulfillment_type" value="pickup" id="type_pickup" onchange="toggleDeliveryOptions()" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="font-bold block text-gray-800">Ambil Sendiri (Pickup)</span>
                                <span class="text-xs text-gray-500">Tanpa Biaya Pengiriman</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Opsi Alamat (Ditampilkan Jika Delivery) -->
                <div id="address_section">
                    <label class="block font-semibold text-gray-700 mb-1">Alamat Pengiriman</label>
                    @if(isset($addresses) && count($addresses) > 0)
                        <select name="address_id" id="address_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="">-- Pilih Alamat Pengiriman --</option>
                            @foreach($addresses as $addr)
                                <option value="{{ $addr->id }}">{{ $addr->label ?? 'Alamat' }} - {{ $addr->address_details }}</option>
                            @endforeach
                        </select>
                    @else
                        <!-- Teks bantuan jika sistem tidak/belum menggunakan tabel khusus addresses -->
                        <textarea name="address_text" placeholder="Masukkan alamat lengkap pengiriman..." class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        <p class="text-xs text-gray-500 mt-1">*Pastikan data alamat pengiriman sudah lengkap.</p>
                    @endif
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Pesanan (Opsional)</label>
                    <textarea name="note" rows="2" placeholder="Contoh: Titip di satpam / pedas sedang" class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <!-- Ringkasan Pesanan -->
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

                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-md font-bold text-lg hover:bg-green-700 transition shadow">
                    Lanjut ke Pembayaran &rarr;
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleDeliveryOptions() {
            const isDelivery = document.getElementById('type_delivery').checked;
            const addressSection = document.getElementById('address_section');
            const addressSelect = document.getElementById('address_id');

            if (isDelivery) {
                addressSection.style.display = 'block';
                if(addressSelect) addressSelect.required = true;
            } else {
                addressSection.style.display = 'none';
                if(addressSelect) addressSelect.required = false;
            }
        }

        // Inisialisasi saat pertama kali dimuat
        document.addEventListener('DOMContentLoaded', toggleDeliveryOptions);
    </script>
</x-app-layout>
