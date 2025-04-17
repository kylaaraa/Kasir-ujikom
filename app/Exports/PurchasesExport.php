<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping
{
    //Fungsi ini ambil semua data dari tabel purchases beserta relasi:
    public function collection()
    {
        return Purchase::with(['member', 'products'])->get();
    }
    //buat  mengatur format baris
    public function map($purchase): array
    {
        $produk = $purchase->products->pluck('nama_produk')->implode(', ');
        $member = $purchase->member;

        return [
            $member->name ?? 'Non Member',
            $member->no_phone ?? '-',
            $member->poin ?? 0,
            $produk,
           'Rp ' . number_format($purchase->total_price, 0, ',', '.'),
            'Rp ' . number_format($purchase->total_bayar, 0, ',', '.'),
            'Rp ' . number_format($purchase->diskon_poin ?? 0, 0, ',', '.'),
            'Rp ' . number_format($purchase->kembalian ?? 0, 0, ',', '.'),

            $purchase->created_at->format('d-m-Y H:i'),
        ];

    //buat set judul dalam kolong excel
    }
    public function headings(): array
    {
        return [
            'Nama Pelanggan',
            'No Telepon',
            'Poin Pelanggan',
            'Produk',
            'Total Harga',
            'Total Bayar',
            'Diskon Poin',
            'Kembalian',
            'Tanggal Pembelian',
        ];
    }
}
