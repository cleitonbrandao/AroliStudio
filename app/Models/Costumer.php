<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Costumer extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id', 'cpf', 'birthday', 'email'
    ];

    public function people(): HasOne
    {
        return $this->hasOne(People::class, 'id', 'person_id');
    }
}
