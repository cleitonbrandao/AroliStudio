<?php

namespace App\Models;

use App\Casts\MonetaryCurrency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Service extends Model implements AuditableContract
{
    use HasFactory, Auditable;
    protected $table = 'services';

    protected $casts = [
        'price' => MonetaryCurrency::class,
        'cost_price' => MonetaryCurrency::class
    ];
    protected $fillable = [
        'team_id',
        'name',
        'service_time',
        'price',
        'cost_price',
        'description'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'packages_services');
    }

    public function scopeAuth(Builder $query): void
    {
        $query->where('team_id', Auth::user()->currentTeam->id);
    }
}
