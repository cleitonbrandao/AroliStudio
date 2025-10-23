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
        $user = Auth::user();
        
        // If user doesn't have a current team, return no results
        if (!$user || !$user->currentTeam) {
            $query->whereNull('team_id');
            return;
        }
        
        $query->where('team_id', $user->currentTeam->id);
    }
}
