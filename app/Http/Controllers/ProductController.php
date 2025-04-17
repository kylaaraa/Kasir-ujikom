<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Ambil parameter pencarian dan jumlah item per halaman dari input form
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); 
    
        // Buat query untuk mengambil produk dengan pencarian berdasarkan nama produk, harga, stok, dll.
        $query = Product::query();
    
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama_produk', 'like', '%' . $search . '%')
                      ->orWhere('harga', 'like', '%' . $search . '%')
                      ->orWhere('stok', 'like', '%' . $search . '%');
            });
        }
    
        // Urutkan berdasarkan tanggal pembuatan secara menurun, lalu paginate sesuai input per_page
        $products = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage)
                          ->appends($request->all());
    
        // Kirim data ke view
        return view('products.index', compact('products'));
    }
    
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|string',
            'stok' => 'required|integer',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Menghilangkan simbol "Rp" dan pemisah ribuan dari harga
        $price = str_replace(['Rp', '.'], '', $request->harga);

        // Simpan gambar dan periksa pathnya
        $imagePath = $request->file('gambar')->store('products', 'public');

        Product::create([
            'nama_produk' => $request->nama_produk,
            'harga' => $price,
            'stok' => $request->stok,
            'gambar' => $imagePath,  // Menyimpan path gambar di database
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Gambar boleh kosong saat edit
        ]);

        // Menghilangkan simbol "Rp" dan pemisah ribuan dari harga
        $price = str_replace(['Rp', '.'], '', $request->harga);

        $updateData = [
            'nama_produk' => $request->nama_produk,
            'harga' => $price,
        ];

        // Cek apakah ada gambar baru yang diunggah
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            Storage::disk('public')->delete($product->gambar);

            // Menyimpan gambar baru
            $updateData['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        // Update data produk
        $product->update($updateData);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function editstok(Product $product)
    {
        return view('products.updatestok', compact('product'));
    }

    public function updatestok(Request $request, Product $product)
    {
        $request->validate([
            'stok' => 'required|integer|min:0', // pakai min:0 biar nggak bisa input angka minus
        ]);

        $product->update([
            'stok' => $request->stok,
        ]);

        return redirect()->route('products.index')->with('success', 'Stok produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar dari storage
        Storage::disk('public')->delete($product->gambar);

        // Hapus produk dari database
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
