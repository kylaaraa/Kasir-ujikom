@extends('layouts.app')

@section('title', 'Produk')
@section('content')
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="bg-white w-full max-w-4xl p-8 rounded-xl shadow-lg relative">
        <h2 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Edit Produk</h2>

        <!-- Tombol close -->
        <a href="{{ route('products.index') }}" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold">
            &times;
        </a>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Form Container -->
            <div class="grid grid-cols-2 gap-8">

                <!-- Left Side (Nama Produk & Harga) -->
                <div class="w-full space-y-6">
                    <!-- Nama Produk -->
                    <div>
                        <label for="nama_produk" class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" required>
                    </div>

                    <!-- Harga -->
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <input type="text" id="harga" name="harga" value="Rp {{ number_format($product->harga, 0, ',', '.') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" required oninput="formatRupiah(this)">
                    </div>
                </div>

                <!-- Right Side (Stok & Gambar) -->
                <div class="w-full space-y-6">
                    <!-- Stok -->
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" id="stok" name="stok" value="{{ $product->stok }}" disabled
                            class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                    </div>

                    <!-- Gambar Produk -->
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('products.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Batal</a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRupiah(element) {
        let value = element.value.replace(/\D/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        element.value = 'Rp ' + formatted;
    }
</script>
@endsection
