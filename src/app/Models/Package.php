<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $casts = [
        'price' => MonetaryCorrency::class
    ];

    protected $fillable = [
        'team_id', 'name', 'price', 'description'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

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
}
