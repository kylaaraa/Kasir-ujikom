{{-- Modal Background --}}
<div id="formCreate" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-5xl p-8 rounded-xl shadow-lg relative">
       <!-- Tombol Close -->
            <button onclick="closeForm()" class="absolute top-4 right-4 text-purple-600 hover:text-purple-800 text-2xl font-bold">&times;</button>

            <!-- Judul Modal -->
            <h2 class="text-3xl font-semibold text-purple-700 mb-8 text-center">Tambah Produk</h2>


        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Form Grid -->
            <div class="grid grid-cols-2 gap-8">
                <!-- Kolom Kiri -->
                <div>
                    <!-- Nama Produk -->
                    <div>
                        <label for="nama_produk" class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-300 focus:border-blue-500 transition duration-200" required>
                    </div>

                    <!-- Harga -->
                    <div class="mt-6">
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <input type="text" id="harga" name="harga" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-300 focus:border-blue-500 transition duration-200" required oninput="formatRupiah(this)">
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <!-- Stok -->
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" id="stok" name="stok" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-300 focus:border-blue-500 transition duration-200" required>
                    </div>

                    <!-- Gambar -->
                    <div class="mt-6">
                        <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-300 focus:border-blue-500 transition duration-200">
                    </div>
                </div>
            </div>

           <!-- Tombol Submit -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-200">
                        Tambah Produk
                    </button>
                </div>

        </form>
    </div>
</div>

<script>
    function openForm() {
        document.getElementById('formCreate').classList.remove('hidden');
    }

    function closeForm() {
        document.getElementById('formCreate').classList.add('hidden');
    }

    function formatRupiah(element) {
        let value = element.value.replace(/\D/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(value);
        element.value = 'Rp ' + formattedValue;
    }
</script>
