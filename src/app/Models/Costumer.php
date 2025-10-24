<?php

namespace App\Models;

use App\Casts\CpfMaskaredWithDataBase;
use App\Casts\DatePtBrWithDataBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Customer extends Model implements AuditableContract
{
    use HasFactory;
    use Auditable;

    protected $casts = [
        'birthday' => DatePtBrWithDataBase::class,
        'cpf' => CpfMaskaredWithDataBase::class
    ];
    
    protected $fillable = [
        'person_id',
        'team_id',
        'cpf',
        'birthday',
        'email'
    ];

    /**
     * Relacionamento com People (N:1)
     * Um customer pertence a uma pessoa
     */
    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class, 'person_id', 'id');
    }

    /**
     * Relacionamento com Team (N:1)
     * Um customer pertence a um team (empresa)
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
     * Scope para busca por nome, email ou CPF
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if (!$search) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
              ->orWhere('cpf', 'like', "%{$search}%")
              ->orWhereHas('people', function ($peopleQuery) use ($search) {
                  $peopleQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Obter nome completo do customer
     */
    public function getFullNameAttribute(): string
    {
        return $this->people 
            ? trim($this->people->name . ' ' . $this->people->last_name) 
            : 'N/A';
    }

    /**
     * Verificar se customer tem todos os dados obrigatórios
     */
    public function isComplete(): bool
    {
        return !empty($this->cpf) 
            && !empty($this->email) 
            && !empty($this->birthday)
            && $this->people !== null;
    }
}
