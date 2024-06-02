<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupsPackage extends Model
{
    use HasFactory;

    protected $table = 'groups_packages';

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'package_origin_id', 'id');
    }
    public function origin(): HasOne
    {
        return $this->hasOne(Package::class);
    }
}
