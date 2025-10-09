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
        return $this->belongsToMany(User::class, 'team_user')
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
        $this->users()->attach($user->id, ['role' => $role]);
    }


    /**
     * Obter role do usuário na empresa
     */
    public function getUserRole(User $user): ?string
    {
        $membership = $this->memberships()->where('user_id', $user->id)->first();
        return $membership ? $membership->role : null;
    }
}
