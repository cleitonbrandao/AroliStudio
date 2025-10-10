<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password',
        'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at',
        'billing_address', 'billing_city', 'billing_state', 
        'billing_postal_code', 'billing_country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Relacionamento com empresas através de memberships (Jetstream)
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Relacionamento com memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Obter empresas onde o usuário é owner
     */
    public function ownedCompanies()
    {
        return $this->companies()->wherePivot('role', 'owner');
    }

    /**
     * Obter empresas onde o usuário é admin
     */
    public function adminCompanies()
    {
        return $this->companies()->wherePivot('role', 'admin');
    }

    /**
     * Obter empresas onde o usuário é member
     */
    public function memberCompanies()
    {
        return $this->companies()->wherePivot('role', 'member');
    }

    /**
     * Verificar se usuário pertence à empresa
     */
    public function belongsToCompany(Company $company): bool
    {
        return $this->companies()->where('team_id', $company->id)->exists();
    }

    /**
     * Obter role do usuário na empresa
     */
    public function getRoleInCompany(Company $company): ?string
    {
        $membership = $this->memberships()->where('team_id', $company->id)->first();
        return $membership ? $membership->role : null;
    }

    /**
     * Verificar se usuário pode gerenciar empresa
     */
    public function canManageCompany(Company $company): bool
    {
        $role = $this->getRoleInCompany($company);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Verificar se usuário é owner da empresa
     */
    public function isOwnerOfCompany(Company $company): bool
    {
        return $this->getRoleInCompany($company) === 'owner';
    }

    /**
     * Relacionamento com assinaturas
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Assinatura ativa do usuário
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('stripe_status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    /**
     * Verificar se usuário tem assinatura ativa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Verificar se está em trial
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Obter limite de empresas do usuário
     */
    public function getCompanyLimit(): int
    {
        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return 1; // Limite gratuito
        }

        $companySubscription = $subscription->companySubscriptions()->first();
        return $companySubscription ? $companySubscription->max_companies : 1;
    }

    /**
     * Verificar se pode criar mais empresas
     */
    public function canCreateCompany(): bool
    {
        $currentCompanies = $this->ownedTeams()->count();
        $limit = $this->getCompanyLimit();
        return $currentCompanies < $limit;
    }
}
