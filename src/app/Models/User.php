<?php
namespace App\Models;
use App\Models\Team;

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
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements AuditableContract
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Auditable;

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


    // Use apenas os relacionamentos padrão do Jetstream (teams, HasTeams)

    /**
     * Relacionamento com memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }


    // Métodos utilitários para teams (empresas) usando Jetstream

    /**
     * Verifica se o usuário pertence ao team (empresa)
     */
    public function belongsToTeam(?Team $team): bool
    {
        if (!$team) {
            return false;
        }
        return $this->teams()->where('team_id', $team->id)->exists();
    }

    /**
     * Obtém o papel do usuário no team (empresa)
     */
    public function getRoleInTeam(Team $team): ?string
    {
        $membership = $this->memberships()->where('team_id', $team->id)->first();
        return $membership ? $membership->role : null;
    }

    /**
     * Verifica se o usuário pode gerenciar o team (empresa)
     */
    public function canManageTeam(Team $team): bool
    {
        $role = $this->getRoleInTeam($team);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Verifica se o usuário é owner do team (empresa)
     */
    public function isOwnerOfTeam(Team $team): bool
    {
        return $this->getRoleInTeam($team) === 'owner';
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

        $companySubscription = $subscription->company()->first();
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
