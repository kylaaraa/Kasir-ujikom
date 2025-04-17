<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'stok',
        'harga',
        'gambar'
    ];

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_product')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }

}
