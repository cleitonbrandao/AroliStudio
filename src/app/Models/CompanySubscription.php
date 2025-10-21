<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'subscription_id',
        'plan_type',
        'max_users',
        'max_companies',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Relacionamento com empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relacionamento com assinatura
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Verificar se está ativa
     */
    public function isActive(): bool
    {
        return $this->is_active &&
               $this->starts_at->isPast() &&
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Verificar se pode adicionar mais usuários
     */
    public function canAddUser(): bool
    {
        return $this->company->current_users < $this->max_users;
    }

    /**
     * Verificar se pode criar mais empresas
     */
    public function canCreateCompany(): bool
    {
        $userCompanies = $this->subscription->user->ownedTeams()->count();
        return $userCompanies < $this->max_companies;
    }

    /**
     * Obter limites do plano
     */
    public function getLimits(): array
    {
        return [
            'max_users' => $this->max_users,
            'max_companies' => $this->max_companies,
            'current_users' => $this->company->current_users,
            'current_companies' => $this->subscription->user->ownedTeams()->count(),
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            });
    }

    public function scopeInactive(Builder $query): void
    {
        $query->where(function ($q) {
            $q->where('is_active', false)
              ->orWhere('starts_at', '>', now())
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('ends_at')
                     ->where('ends_at', '<=', now());
              });
        });
    }
}
