<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageProduct extends Model
{
    use HasFactory;

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'package_service_product_id', 'id');
    }
}
