<?php

namespace App\Models;

use App\Casts\MonetaryCurrency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $casts = [
        'price' => MonetaryCurrency::class
    ];

    protected $fillable = [
        'name', 'price', 'description'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'packages_products');
    }
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'packages_services');
    }
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(GroupsPackage::class, 'groups_packages', 'package_origin_id', 'package_id');
    }

    public function scopeAuth(Builder $query): void
    {
        $query->where('team_id', Auth::user()->currentTeam->id);
    }
}
