<?php

namespace App\Http\Controllers;

use App\Exports\PurchasesExport;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = Purchase::query();

        // Pencarian berdasarkan nama member
        if ($searchName = $request->input('search_name')) {
            $query->whereHas('member', function ($q) use ($searchName) {
                $q->where('name', 'like', '%' . $searchName . '%');
            });
        }

        // Pencarian berdasarkan total harga
        if ($searchPrice = $request->input('search_price')) {
            $query->where('total_price', 'like', '%' . $searchPrice . '%');
        }

        // Pencarian berdasarkan tanggal pembelian
        if ($searchDate = $request->input('search_date')) {
            $query->whereDate('created_at', '=', $searchDate);
        }

        $purchases = $query->with('member', 'user')
                           ->orderBy('created_at', 'desc')
                           ->paginate($perPage)
                           ->appends($request->all());

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::all();
        return view('purchases.create', compact('products'));
    }

    public function confirm(Request $request)
    {
        $data = $request->input('products');
        $selected = [];
        $total_price = 0;

        foreach ($data as $product) {
            if ($product['qty'] > 0) {
                $productData = Product::find($product['id']);
                if ($product['qty'] > $productData->stok) {
                    return back()->withErrors(['msg' => 'Stok tidak mencukupi untuk ' . $productData->nama_produk]);
                }

                $subtotal = $product['qty'] * $productData->harga;

                $selected[] = [
                    'id' => $product['id'],
                    'nama_produk' => $productData->nama_produk,
                    'harga' => $productData->harga,
                    'qty' => $product['qty'],
                    'subtotal' => $subtotal,
                ];

                $total_price += $subtotal;
            }
        }

        if (count($selected) === 0) {
            return back()->withErrors(['msg' => 'Pilih minimal 1 produk.']);
        }

        session([
            'purchase_data' => [
                'products' => $selected,
                'total_price' => $total_price,
            ]
        ]);

        return redirect()->route('purchases.member');
    }

    public function memberForm()
    {
        $data = session('purchase_data');
        if (!$data) return redirect()->route('purchases.create');

        return view('purchases.member', compact('data'));
    }

    public function finish(Request $request)
    {
        $data = session('purchase_data');
        if (!$data) return redirect()->route('purchases.create');

        $request->validate([
            'total_payment' => 'required',
            'is_member' => 'required|boolean',
            'no_phone' => 'nullable|numeric',
            'name' => 'nullable|string|max:255',
            'use_points_checkbox' => 'nullable|boolean',
        ]);

        $member_id = null;
        $diskon_poin = 0;
        $total_price = $data['total_price'];
        $memberController = new MemberController();

        if ($request->is_member) {
            // Buat atau ambil member
            $member = $memberController->getOrCreate($request);

            // Gunakan poin jika checkbox dicentang
            if ($request->has('use_points_checkbox')) {
                $result = $memberController->applyPoints($member, $total_price);
                $total_price = $result['total_price'];
                $diskon_poin = $result['diskon_poin'];
            }

            $member_id = $member->id;

            // Tambahkan poin dari total harga setelah diskon
            $memberController->addPoints($member, $total_price);
        }

        // Convert total pembayaran jadi integer
        $total_payment = (int) str_replace('.', '', $request->total_payment);

        // Validasi total pembayaran
        $request->validate([
            'total_payment' => 'required|integer|min:' . $total_price,
        ]);

        $kembalian = max(0, $total_payment - $total_price);

        $purchase = Purchase::create([
            'member_id'     => $member_id,
            'total_price'   => $total_price,
            'diskon_poin'   => $diskon_poin,
            'total_bayar'   => $total_payment,
            'kembalian'     => $kembalian,
            'purchase_date' => now(),
            'created_by'    => Auth::id(),
        ]);

        foreach ($data['products'] as $prod) {
            $purchase->products()->attach($prod['id'], [
                'quantity' => $prod['qty'],
                'subtotal' => $prod['subtotal'],
            ]);

            Product::find($prod['id'])->decrement('stok', $prod['qty']);
        }

        session()->forget('purchase_data');

        return redirect()->route('purchases.receipt', ['id' => $purchase->id]);
    }

    public function receipt($id)
    {
        $purchase = Purchase::with('products')->findOrFail($id);
        return view('purchases.receipt', compact('purchase'));
    }

    public function show($id)
    {
        $purchase = Purchase::with('member', 'user', 'products')->findOrFail($id);
        return view('purchases.receipt', compact('purchase'));
    }

    public function downloadReceipt($id)
    {
        $purchase = Purchase::with(['member', 'user', 'products'])->findOrFail($id);
        $pdf = Pdf::loadView('purchases.receipt_pdf', compact('purchase'));
        return $pdf->download('bukti-pembelian-' . $purchase->id . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PurchasesExport, 'data_pembelian.xlsx');
    }
}
