<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageProduct extends Model
{
    use HasFactory;
    protected $table = 'packages_products';

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}
