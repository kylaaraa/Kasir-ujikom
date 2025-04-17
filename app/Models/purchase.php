<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $fillable = [
        'member_id',
        'total_price',
        'purchase_date',
        'diskon_poin',
        'total_bayar',
        'kembalian',
        'created_by'
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_product')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }
}
