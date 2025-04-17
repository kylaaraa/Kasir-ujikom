<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalPenjualan = null;
        $chartData = [];
        $totalPerProduk = [];
        $stokPerProduk = [];
        $memberStats = [];

        if ($user->role === 'petugas') {
            $totalPenjualan = Purchase::whereDate('created_at', Carbon::today())->count();
        }

        if ($user->role === 'admin') {
            // Chart Pie: Stok Produk
            $stokPerProduk = DB::table('products')
                ->select('nama_produk', 'stok')
                ->pluck('stok', 'nama_produk')
                ->toArray();

            // Bar Chart: Jumlah transaksi member vs non-member
            $jumlahTransaksiMember = Purchase::whereNotNull('member_id')
                ->distinct('member_id')
                ->count('member_id');

            $jumlahTransaksiNonMember = Purchase::whereNull('member_id')->count();

            $memberStats = [
                'Member' => $jumlahTransaksiMember,
                'Non-Member' => $jumlahTransaksiNonMember,
            ];

            // Grafik bar (jumlah total produk terjual selama 7 hari terakhir)
            $chartData = DB::table('purchase_product')
                ->join('purchases', 'purchase_product.purchase_id', '=', 'purchases.id')
                ->select(DB::raw('SUM(purchase_product.quantity) as total'))
                ->whereDate('purchases.created_at', '>=', Carbon::now()->subDays(6)->toDateString())
                ->get()
                ->map(function ($item) {
                    return [
                        'total' => $item->total,
                    ];
                })
                ->toArray();

            // Grafik pie: total penjualan per produk
            $totalPerProduk = DB::table('purchase_product')
                ->join('products', 'purchase_product.product_id', '=', 'products.id')
                ->select('products.nama_produk', DB::raw('SUM(purchase_product.quantity) as total'))
                ->groupBy('products.nama_produk')
                ->pluck('total', 'products.nama_produk')
                ->toArray();
        }

        return view('dashboard', compact(
            'user',
            'totalPenjualan',
            'chartData',
            'totalPerProduk',
            'stokPerProduk',
            'memberStats'
        ));
    }
}
