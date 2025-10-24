<?php

namespace App\Models;

use Dom\Attr;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Team extends JetstreamTeam implements AuditableContract
{
    use HasFactory, Auditable;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_team' => 'boolean',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'personal_team',
        'locale',
        'max_users',
        'current_users',
        'plan_type',
        'is_active',
        'trial_ends_at',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected static function booted(): void
    {
        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = $team->factorySlug();
            }
        });

        static::updating(function ($team) {
            if ($team->isDirty('name') || empty($team->slug)) {
                $team->slug = $team->factorySlug();
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
        $base = Str::slug($this->name ?: 'team');
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
     * Get the locale for this team.
     * Falls back to app default if not set.
     */
    public function locale(): Attribute
    {
        return new Attribute(
            get: fn($value): string => $value,
            set: fn($value): string => $value ?? config('app.locale', 'pt_BR'),
        );
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
        return $this->hasMany(CompanySubscription::class, 'company_id');
    }

    /**
     * Verificar se tem assinatura ativa
     */
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

    /**
     * Remove the given user from the team.
     * 
     * Override Jetstream's method to automatically switch to next available team
     * instead of setting current_team_id to null.
     */
    public function removeUser($user): void
    {
        // Detach user from team first
        $this->users()->detach($user);

        // If the removed user had this team as current, update to another team or null
        if ($user->current_team_id === $this->id) {
            // Try to find another team (after detachment, so this team won't be included)
            $nextTeam = $user->teams()->first();
            
            $user->forceFill([
                'current_team_id' => $nextTeam?->id,
            ])->save();
        }
    }
}
