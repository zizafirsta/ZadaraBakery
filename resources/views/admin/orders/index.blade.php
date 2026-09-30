<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Transaksi Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-3">No. Order</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b">
                            <td class="p-3 font-semibold">{{ $order->order_number }}</td>
                            <td class="p-3">{{ $order->user->name }} ({{ $order->user->email }})</td>
                            <td class="p-3 font-bold text-amber-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="bg-indigo-600 text-white text-xs px-3 py-1 rounded font-semibold hover:bg-indigo-700">Kelola</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
