<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

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

    public function scopeSearch(Builder $query,string $like)
    {
        return $query->where('name', 'like', '%' . $like . '%');
    }
}
