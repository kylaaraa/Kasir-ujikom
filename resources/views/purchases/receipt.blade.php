@extends('layouts.app')

@section('title', 'Pembelian')
@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-xl">

    <!-- Detail Pembelian -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <p class="text-lg font-semibold">Nomor Transaksi: #{{ $purchase->id }}</p>
            <p class="text-sm text-gray-600">{{ $purchase->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</p>
        </div>

        <!-- Detail Member (Jika Ada) -->
        @if ($purchase->member)
        <div class="mb-6">
            <h3 class="text-lg font-semibold">Detail Member</h3>
            <p class="text-sm text-gray-600">Nama: {{ $purchase->member->name }}</p>
            <p class="text-sm text-gray-600">No. Telepon: {{ $purchase->member->no_phone }}</p>
            <p class="text-sm text-gray-600">Poin yang Digunakan: Rp {{ number_format($purchase->diskon_poin, 0, ',', '.') }}
            </p>
            <p class="text-sm text-gray-600">Poin yang Tersisa: {{ number_format($purchase->member->poin, 0, ',', '.') }}</p>
        </div>
        @endif

        <!-- Rincian Produk yang Dibeli -->
        <table class="w-full border text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Produk</th>
                    <th class="border px-4 py-2">Qty</th>
                    <th class="border px-4 py-2">Harga</th>
                    <th class="border px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->products as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->nama_produk }}</td>
                    <td class="border px-4 py-2">{{ $item->pivot->quantity }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($item->pivot->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="3" class="border px-4 py-2 text-right">Total Pembelian</td>
                    <td class="border px-4 py-2" id="total-price">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Rincian Pembayaran -->
    <div class="mb-6">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Detail Pembayaran</th>
                    <th class="border px-4 py-2">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-4 py-2">Total Harga</td>
                    <td class="border px-4 py-2">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="border px-4 py-2">Pembayaran</td>
                    <td class="border px-4 py-2">Rp {{ number_format($purchase->total_bayar, 0, ',', '.') }}</td>
                </tr>
                @if ($purchase->kembalian !== null)
                <tr class="font-semibold">
                    <td class="border px-4 py-2">Kembalian</td>
                    <td class="border px-4 py-2 text-green-600">
                        Rp {{ number_format($purchase->kembalian, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

            </tbody>
        </table>
    </div>

    <!-- Pesan Terima Kasih dan Link Kembali -->
    <div class="mt-6 text-right"> <!-- Ubah text-center menjadi text-right -->
        <a href="{{ route('purchases.index') }}" class="text-blue-600 hover:underline mt-4 inline-block">Kembali</a>
    </div>
</div>
@endsection