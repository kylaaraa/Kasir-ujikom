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
        //Mengambil data user yang sedang login.
        $totalPenjualan = null;
        $chartData = [];
        //Ini untuk grafik bar (batang) yang menampilkan jumlah produk yang terjual tiap hari selama 7 hari terakhir.
        $totalPerProduk = [];
        //Ini untuk grafik pie (lingkaran) yang menampilkan total penjualan tiap produk secara keseluruhan.

        if ($user->role === 'petugas') {
            $totalPenjualan = Purchase::whereDate('created_at', Carbon::today())->count();
        }

        if ($user->role === 'admin') {
            // Data untuk grafik bar (penjualan per tanggal dan produk 7 hari terakhir)
            $salesPerDayRaw = DB::table('purchase_product') 
                ->join('purchases', 'purchase_product.purchase_id', '=', 'purchases.id')
                ->join('products', 'purchase_product.product_id', '=', 'products.id')
                ->select(
                    DB::raw('DATE(purchases.created_at) as tanggal'),
                    'products.nama_produk',
                    DB::raw('SUM(purchase_product.quantity) as total')
                )
                ->whereDate('purchases.created_at', '>=', Carbon::now()->subDays(6)->toDateString())
                ->groupBy('tanggal', 'products.nama_produk')
                ->orderBy('tanggal')
                ->distinct() // Menghindari duplikasi data
                ->get()
                ->groupBy('tanggal');


            foreach ($salesPerDayRaw as $tanggal => $items) {
                $entry = ['tanggal' => $tanggal];
                foreach ($items as $item) {
                    $entry[$item->nama_produk] = $item->total;
                }
                $chartData[] = $entry;
            }

            // Data untuk grafik pie (total penjualan per produk)
            $totalPerProduk = DB::table('purchase_product')
                ->join('products', 'purchase_product.product_id', '=', 'products.id')
                ->select('products.nama_produk', DB::raw('SUM(purchase_product.quantity) as total'))
                ->groupBy('products.nama_produk')
                ->pluck('total', 'products.nama_produk')
                ->toArray();
        }

        return view('dashboard', compact('user', 'totalPenjualan', 'chartData', 'totalPerProduk'));
    }
}
