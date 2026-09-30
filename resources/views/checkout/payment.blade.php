<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-xl mx-auto px-4 text-center">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Pesanan Dibuat!</h3>
            <p class="text-gray-600 mb-4">No. Transaksi: <span class="font-semibold">{{ $order->order_number }}</span></p>

            <p class="text-2xl font-extrabold text-amber-700 mb-6">
                Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
            </p>

            <button id="pay-button" class="bg-amber-600 text-white px-8 py-3 rounded-md font-bold text-lg hover:bg-amber-700 transition">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <!-- Script Snap Midtrans -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result){
                    alert("Pembayaran Berhasil!");
                    window.location.href = "{{ route('shop.index') }}";
                },
                onPending: function(result){
                    alert("Menunggu Pembayaran...");
                    window.location.href = "{{ route('shop.index') }}";
                },
                onError: function(result){
                    alert("Pembayaran Gagal!");
                },
                onClose: function(){
                    alert('Anda menutup halaman pembayaran sebelum selesai.');
                }
            });
        });
    </script>
</x-app-layout>
