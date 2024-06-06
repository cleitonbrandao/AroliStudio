<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $casts = [
        'price' => MonetaryCorrency::class,
        'cost_price' => MonetaryCorrency::class
    ];
    protected $table = 'products';

    protected $fillable = [
        'name', 'price', 'cost_price', 'description'
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packages_products');
    }
}
