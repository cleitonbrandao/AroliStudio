<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'inscricao_estatual',
        'bussines_email'
    ];

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(EnterprisePeople::class, 'enterprises_peoples', 'enterprise_id', 'people_id');
    }
}
