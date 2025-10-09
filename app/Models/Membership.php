<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Membership as JetstreamMembership;

class Membership extends JetstreamMembership
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'role',
    ];

    /**
     * Tabela que o modelo usa
     */
    protected $table = 'team_user';

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com empresa (team)
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    /**
     * Alias para company (compatibilidade)
     */
    public function company(): BelongsTo
    {
        return $this->team();
    }

    /**
     * Verificar se é owner
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Verificar se é admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar se é member
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Verificar se pode gerenciar membros
     */
    public function canManageMembers(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    /**
     * Verificar se pode editar empresa
     */
    public function canEditCompany(): bool
    {
        return $this->role === 'owner';
    }
}
