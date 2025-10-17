<?php

namespace App\Models;

use App\Casts\MonetaryCurrency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';

    protected $casts = [
        'price' => MonetaryCurrency::class,
        'cost_price' => MonetaryCurrency::class
    ];
    protected $fillable = [
        'name', 'service_time', 'price', 'cost_price', 'description'
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'packages_services');
    }
    
    public function scopeAuth(Builder $query): void
    {
        $query->where('team_id', Auth::user()->currentTeam->id);
    }
}
