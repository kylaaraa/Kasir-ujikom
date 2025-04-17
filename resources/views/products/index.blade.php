@extends('layouts.app')

@section('title', 'Produk')
@section('content')

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Manajemen Produk</h2>

    {{-- Pencarian & Tambah Produk --}}
    <div class="mb-6 flex justify-between items-center">
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center w-64">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search..."
                class="px-4 py-2 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
            >
            <button
                type="submit"
                class="border border-purple-600 text-purple-600 py-2 px-4 rounded hover:bg-purple-600 hover:text-white transition text-base"
            >
                Search
            </button>
        </form>

        <div class="flex items-center gap-4">
            @if(auth()->check() && auth()->user()->role == 'admin')
                <button 
                    onclick="openForm()" 
                    class="border border-purple-600 text-purple-600 py-2 px-4 rounded hover:bg-purple-600 hover:text-white transition text-base"
                >
                    Tambah Produk
                </button>
            @endif

            <form action="{{ route('products.index') }}" method="GET" class="mb-0">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <select 
                    name="per_page" 
                    onchange="this.form.submit()" 
                    class="px-4 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                >
                    <option value="10"  {{ request('per_page') == 10  ? 'selected' : '' }}>10</option>
                    <option value="25"  {{ request('per_page') == 25  ? 'selected' : '' }}>25</option>
                    <option value="50"  {{ request('per_page') == 50  ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Tabel Produk --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full table-auto text-base text-center border border-gray-200">
            <thead class="bg-purple-600 text-white">
                <tr>
                    <th class="px-4 py-3 border-b">Gambar</th>
                    <th class="px-4 py-3 border-b">Nama Produk</th>
                    <th class="px-4 py-3 border-b">Harga</th>
                    <th class="px-4 py-3 border-b">Stok</th>
                    @if(auth()->check() && auth()->user()->role == 'admin')
                        <th class="px-4 py-3 border-b">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-4 py-3">
                            <img src="{{ asset('storage/' . $product->gambar) }}" class="w-32 h-32 object-cover rounded-md mx-auto">
                        </td>
                        <td class="px-4 py-3">{{ $product->nama_produk }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $product->stok }}</td>
                        @if(auth()->check() && auth()->user()->role == 'admin')
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="border border-yellow-500 text-yellow-500 py-2 px-4 rounded hover:bg-yellow-500 hover:text-white transition text-base">
                                        Edit
                                    </a>
                                    <a href="{{ route('products.editstok', $product->id) }}"
                                       class="border border-blue-500 text-blue-500 py-2 px-4 rounded hover:bg-blue-500 hover:text-white transition text-base">
                                        Update Stok
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="border border-red-500 text-red-500 py-2 px-4 rounded hover:bg-red-500 hover:text-white transition text-base">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>

@include('products.create')

@endsection
