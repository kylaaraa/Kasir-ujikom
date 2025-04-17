@extends('layouts.app')

@section('title', 'Pembelian')
@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Pilih Produk</h2>

    <form action="{{ route('purchases.confirm') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($products as $product)
            <div class="border rounded-2xl p-4 text-center bg-white shadow">
                {{-- Gambar produk ditampilkan full tanpa kepotong --}}
                <div class="aspect-[4/3] w-full mb-4 bg-gray-100 rounded overflow-hidden">
                    <img src="{{ asset('storage/' . $product->gambar) }}"
                        class="w-full h-full object-contain" alt="{{ $product->nama_produk }}">
                </div>

                <h3 class="text-xl font-semibold mb-1">{{ $product->nama_produk }}</h3>
                <p class="text-gray-500 text-sm mb-1">Stok {{ $product->stok }}</p>
                <p class="text-lg font-medium mb-4">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>

                {{-- Data untuk dikirim ke backend --}}
                <input type="hidden" name="products[{{ $product->id }}][id]" value="{{ $product->id }}">
                <input type="hidden" id="price_{{ $product->id }}" value="{{ $product->harga }}">
                <input type="hidden" name="products[{{ $product->id }}][qty]" id="qty_input_{{ $product->id }}" value="0">
                <input type="hidden" name="products[{{ $product->id }}][subtotal]" id="subtotal_input_{{ $product->id }}" value="0">

                {{-- Tombol tambah/kurang jumlah --}}
                <div class="flex justify-center items-center gap-4 mb-2">
                    <button type="button" onclick="updateQty({{ $product->id }}, -1, {{ $product->stok }})" class="text-2xl px-3">−</button>
                    <span id="qty_display_{{ $product->id }}" class="text-lg font-medium">0</span>
                    <button type="button" onclick="updateQty({{ $product->id }}, 1, {{ $product->stok }})" class="text-2xl px-3">+</button>
                </div>

                {{-- Menampilkan subtotal harga untuk produk ini --}}
                <p class="text-gray-600">Sub Total <span class="font-semibold text-black" id="subtotal_display_{{ $product->id }}">Rp. 0</span></p>
            </div>
            @endforeach
        </div>

        {{-- Tombol submit --}}
        <div class="mt-8 text-center">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Lanjutkan Pembelian
            </button>
        </div>
    </form>
</div>

{{-- Script untuk mengatur qty dan subtotal --}}
<script>
    function updateQty(id, change, stok) {
        const qtyInput = document.getElementById(`qty_input_${id}`); // buat ngirim ke backend
        const qtyDisplay = document.getElementById(`qty_display_${id}`); // buat nampilin jumlah ke user
        const price = parseInt(document.getElementById(`price_${id}`).value); // harga
        const subtotalInput = document.getElementById(`subtotal_input_${id}`); // buat ngitung subtotal
        const subtotalDisplay = document.getElementById(`subtotal_display_${id}`); // nampilin subtotal ke user

        let qty = parseInt(qtyInput.value) || 0;
        qty += change; // bisa +1 atau -1

        if (qty < 0) qty = 0; // ga boleh kurang dari 0
        if (qty > stok) qty = stok; // ga boleh melebihi stok yang ada pokoknya

        qtyInput.value = qty;
        qtyDisplay.textContent = qty;
        const subtotal = qty * price;
        subtotalInput.value = subtotal;
        subtotalDisplay.textContent = "Rp. " + subtotal.toLocaleString('id-ID'); // format ke rupiah
    }
</script>
@endsection









