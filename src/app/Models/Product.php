<?php

namespace App\Models;

use App\Casts\MonetaryCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;
    protected $casts = [
        'price' => MonetaryCurrency::class,
        'cost_price' => MonetaryCurrency::class
    ];
    protected $table = 'products';

    protected $fillable = [
        'team_id', 'name', 'price', 'cost_price', 'description'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packages_products');
    }

    public function scopeAuth($query)
    {
        $query->where('team_id', Auth::user()->currentTeam->id);
    }
}
