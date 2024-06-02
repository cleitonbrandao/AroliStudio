<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $casts = [
        'price' => MonetaryCorrency::class
    ];

    protected $fillable = [
        'name', 'price', 'description'
    ];

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(PackageService::class, Package::class);
    }
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(PackageProduct::class, PackageProduct::class);
    }
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(GroupsPackage::class, 'groups_packages', 'package_origin_id', 'id');
    }
}
