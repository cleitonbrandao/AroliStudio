<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
