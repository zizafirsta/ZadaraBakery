<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keranjang Belanja Anda') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($cart) > 0)
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b text-left text-gray-600">
                            <th class="py-3">Produk</th>
                            <th class="py-3">Harga</th>
                            <th class="py-3">Jumlah</th>
                            <th class="py-3">Subtotal</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($cart as $id => $details)
                            @php $subtotal = $details['price'] * $details['quantity']; $total += $subtotal; @endphp
                            <tr class="border-b">
                                <td class="py-4 flex items-center space-x-4">
                                    @if($details['image'])
                                        <img src="{{ asset('storage/' . $details['image']) }}" class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <span class="font-semibold text-gray-800">{{ $details['name'] }}</span>
                                </td>
                                <td class="py-4">Rp {{ number_format($details['price'], 0, ',', '.') }}</td>
                                <td class="py-4">{{ $details['quantity'] }} pcs</td>
                                <td class="py-4 font-bold text-amber-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td class="py-4 text-center">
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-8 flex justify-between items-center border-t pt-6">
                    <div>
                        <a href="{{ route('shop.index') }}" class="text-amber-600 font-semibold hover:underline">&larr; Lanjut Belanja</a>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-extrabold text-gray-800 mb-4">Total: <span class="text-amber-700">Rp {{ number_format($total, 0, ',', '.') }}</span></p>
                        <a href="#" class="bg-green-600 text-white px-6 py-3 rounded-md font-bold hover:bg-green-700 transition">Lanjut ke Pembayaran (Checkout) &rarr;</a>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Keranjang belanja Anda masih kosong.</p>
                    <a href="{{ route('shop.index') }}" class="mt-4 inline-block bg-amber-600 text-white px-6 py-2 rounded-md font-semibold">Mulai Belanja</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
