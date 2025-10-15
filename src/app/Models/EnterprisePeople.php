<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnterprisePeople extends Model
{
    use HasFactory;
    protected $table = 'enterprises_peoples';

    public function owners(): HasMany
    {
        return $this->hasMany(People::class, 'enterprise_id', 'id');
    }

}
