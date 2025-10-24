<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class People extends Model
{
    use HasFactory;
    
    protected $table = 'peoples';
    
    protected $fillable = [
        'team_id',
        'name',
        'last_name',
        'phone',
        'photo'
    ];

    /**
     * Relacionamento com Customer (1:1)
     * Uma pessoa pode ser um customer
     */
    public function costumer(): HasOne
    {
        return $this->hasOne(Costumer::class, 'person_id', 'id');
    }

    /**
     * Relacionamento com Team (N:1)
     * Uma pessoa pertence a um team
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * Scope para filtrar por team autenticado
     */
    public function scopeAuth(Builder $query): void
    {
        $user = Auth::user();
        
        if (!$user || !$user->currentTeam) {
            $query->whereNull('team_id');
            return;
        }
        
        $query->where('team_id', $user->currentTeam->id);
    }

    /**
     * Scope para busca por nome ou telefone
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if (!$search) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Obter nome completo
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    /**
     * Verificar se é um customer
     */
    public function isCustomer(): bool
    {
        return $this->costumer()->exists();
    }
}
