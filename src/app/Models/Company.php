<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected static function booted(): void
    {
        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = $company->factorySlug();
            }
        });

        static::updating(function ($company) {
            if ($company->isDirty('name') || empty($company->slug)) {
                $company->slug = $company->factorySlug();
            }
        });
    }

    public function slugIsUnique(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return !$query->exists();
    }

    public function factorySlug(): string
    {
        $maxLength = 255;
        $base = Str::slug($this->name ?: 'company');
        $base = Str::limit($base, $maxLength, '');

        $slug = $base;
        $i = 1;

        while (!$this->slugIsUnique($slug)) {
            $suffix = '-' . $i++;
            $allowed = $maxLength - strlen($suffix);
            $slug = Str::limit($base, max(1, $allowed), '') . $suffix;
        }

        return $slug;
    }

    /**
     * Relacionamento com usuários através de memberships (Jetstream)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')->withPivot('role');
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
     * Obter cargo do usuário na empresa
     */
    public function getUserRole(User $user): ?string
    {
        $membership = $this->memberships()
            ->where('user_id', $user->id)
            ->first();

        return $membership ? $membership->role : null;
    }

    /**
     * Relacionamento com assinaturas da empresa
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->active()
            ->exists();
    }

    /**
     * Assinatura ativa da empresa
     */
    public function activeSubscription(): ?CompanySubscription
    {
        return $this->subscriptions()
            ->active()
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
