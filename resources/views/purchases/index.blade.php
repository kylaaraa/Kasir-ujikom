@extends('layouts.app')

@section('title', 'Pembelian')
@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Data Pembelian</h2>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded text-base">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Pencarian --}}
    <div class="mb-6 flex justify-between items-center">
        {{-- Pencarian di kiri --}}
        <form action="{{ route('purchases.index') }}" method="GET" class="flex items-center w-64">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search.."
                class="px-4 py-2 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
            >
            <button
                type="submit"
                class="border border-purple-600 text-purple-600 py-2 px-4 rounded hover:bg-purple-600 hover:text-white transition text-base"
            >
                Search
            </button>
        </form>

        {{-- Tombol Tambah Pembelian, Export Excel, dan Select --}}
        <div class="flex items-center gap-4">
            @if(auth()->check() && auth()->user()->role == 'petugas')
                <a href="{{ route('purchases.create') }}"
                   class="border border-purple-600 text-purple-600 py-2 px-4 rounded hover:bg-purple-600 hover:text-white transition text-base">
                    Tambah Pembelian
                </a>
            @endif

            <a href="{{ route('purchases.export.excel') }}"
               class="border border-green-600 text-green-600 py-2 px-4 rounded hover:bg-green-600 hover:text-white transition text-base">
                Export Excel
            </a>

            <form action="{{ route('purchases.index') }}" method="GET" class="mb-0">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <select name="per_page" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Tabel Data Pembelian --}}
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full table-auto text-base text-center border-collapse">
            <thead class="bg-purple-600 text-white">
                <tr>
                    <th class="px-4 py-3 border-b">Nama Pelanggan</th>
                    <th class="px-4 py-3 border-b">Tanggal</th>
                    <th class="px-4 py-3 border-b">Total Harga</th>
                    <th class="px-4 py-3 border-b">Dibuat Oleh</th>
                    <th class="px-4 py-3 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($purchases as $purchase)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-4 py-3">
                            {{ $purchase->member->name ?? 'Non Member' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $purchase->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $purchase->user->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('purchases.receipt', $purchase->id) }}"
                                   class="border border-purple-600 text-purple-600 px-3 py-1 rounded hover:bg-purple-600 hover:text-white transition text-base">
                                    Detail
                                </a>
                                <a href="{{ route('purchases.download', $purchase->id) }}"
                                   class="border border-purple-600 text-purple-600 px-3 py-1 rounded hover:bg-purple-600 hover:text-white transition text-base">
                                    Unduh
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500 text-base">
                            Belum ada data pembelian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
