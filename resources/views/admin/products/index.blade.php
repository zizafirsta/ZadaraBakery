<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Produk Roti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Daftar Roti</h3>
                    <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-indigo-700">+ Tambah Roti</a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3 border">Foto</th>
                            <th class="p-3 border">Nama Roti</th>
                            <th class="p-3 border">Kategori</th>
                            <th class="p-3 border">Harga</th>
                            <th class="p-3 border">Stok</th>
                            <th class="p-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="p-3 border">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-16 h-16 object-cover rounded">
                                    @else
                                        <span class="text-gray-400 italic">Tanpa Foto</span>
                                    @endif
                                </td>
                                <td class="p-3 border font-semibold">{{ $product->name }}</td>
                                <td class="p-3 border">{{ $product->category->name }}</td>
                                <td class="p-3 border">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="p-3 border">{{ $product->stock }} pcs</td>
                                <td class="p-3 border text-center space-x-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus roti ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">Belum ada data roti.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
