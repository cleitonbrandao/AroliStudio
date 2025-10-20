<?php

namespace App\Models;

use App\Casts\CpfMaskaredWithDataBase;
use App\Casts\DatePtBrWithDataBase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Costumer extends Model implements AuditableContract
{
    use HasFactory;
    use Auditable;

    protected $casts = [
        'birthday' => DatePtBrWithDataBase::class,
        'cpf' => CpfMaskaredWithDataBase::class
    ];
    protected $fillable = [
        'person_id', 'cpf', 'birthday', 'email'
    ];

    public function people(): HasOne
    {
        return $this->hasOne(People::class, 'id', 'person_id');
    }
}
