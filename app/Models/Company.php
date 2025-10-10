<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Jetstream\Team as JetstreamTeam;

class Company extends JetstreamTeam
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'personal_team',
        'max_users',
        'current_users',
        'plan_type',
        'is_active',
        'trial_ends_at',
    ];

    /**
     * Tabela que o modelo usa
     */
    protected $table = 'teams';

    protected static function boot()
    {
        parent::boot();
        
        // Gerar slug automaticamente
        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
                
                // Garantir unicidade do slug
                $originalSlug = $company->slug;
                $counter = 1;
                while (static::where('slug', $company->slug)->exists()) {
                    $company->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }

    /**
     * Relacionamento com usuários através de memberships (Jetstream)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Relacionamento com memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'team_id');
    }

    /**
     * Obter usuários com role específico
     */
    public function getUsersByRole(string $role)
    {
        return $this->users()->wherePivot('role', $role);
    }

    /**
     * Obter owners da empresa
     */
    public function owners()
    {
        return $this->getUsersByRole('owner');
    }

    /**
     * Obter admins da empresa
     */
    public function admins()
    {
        return $this->getUsersByRole('admin');
    }

    /**
     * Obter members da empresa
     */
    public function members()
    {
        return $this->getUsersByRole('member');
    }

    /**
     * Adicionar usuário à empresa
     */
    public function addUser(User $user, string $role = 'member'): void
    {
        $this->users()->attach($user->id, [
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    /**
     * Obter role do usuário na empresa
     */
    public function getUserRole(User $user): ?string
    {
        $membership = $this->memberships()->where('user_id', $user->id)->first();
        return $membership ? $membership->role : null;
    }

    /**
     * Relacionamento com assinaturas da empresa
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    /**
     * Assinatura ativa da empresa
     */
    public function activeSubscription(): ?CompanySubscription
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    /**
     * Verificar se pode adicionar mais usuários
     */
    public function canAddUser(): bool
    {
        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return $this->current_users < $this->max_users;
        }
        return $this->current_users < $subscription->max_users;
    }

    /**
     * Adicionar usuário e atualizar contador
     */
    public function addUserWithCount(User $user, string $role = 'member'): void
    {
        $this->addUser($user, $role);
        $this->increment('current_users');
    }

    /**
     * Remover usuário e atualizar contador
     */
    public function removeUserWithCount(User $user): void
    {
        $this->users()->detach($user->id);
        $this->decrement('current_users');
    }

    /**
     * Obter limite de usuários
     */
    public function getUserLimit(): int
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->max_users : $this->max_users;
    }

    /**
     * Verificar se empresa está ativa
     */
    public function isActive(): bool
    {
        return $this->is_active && 
               ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }
}
