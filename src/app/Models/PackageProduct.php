<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PackageProduct extends Model
{
    use HasFactory;
    protected $table = 'packages_products';

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
    public function scopeAuth(Builder $query): void
    {
        $query->where('team_id', Auth::user()->currentTeam->id);
    }
}
