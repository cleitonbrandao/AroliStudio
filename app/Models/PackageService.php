<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageService extends Model
{
    use HasFactory;

    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'package_service_service_id', 'id');
    }
}
