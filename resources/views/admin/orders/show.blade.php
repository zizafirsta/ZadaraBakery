<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Order: ') . $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div>
                    <h3 class="font-bold text-gray-700">Data Pemesan:</h3>
                    <p>{{ $order->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                    <p class="text-sm text-gray-500">HP: {{ $order->phone }}</p>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700">Alamat Pengiriman:</h3>
                    <p class="text-gray-600 text-sm">{{ $order->address }}</p>
                </div>
            </div>

            <!-- Form Ubah Status Transaksi -->
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="bg-gray-50 p-4 rounded-md flex items-center justify-between">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-gray-700">Ubah Status Pembayaran Manual:</label>
                    <select name="status" class="border-gray-300 rounded-md shadow-sm mt-1">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                        <option value="failed" {{ $order->status == 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-indigo-700">Update Status</button>
            </form>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 font-semibold hover:underline">&larr; Kembali ke Daftar Order</a>
                <p class="text-xl font-extrabold text-amber-700">Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>

        </div>
    </div>
</x-app-layout>
