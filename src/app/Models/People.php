<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class People extends Model
{
    use HasFactory;
    protected $table = 'peoples';
    protected $fillable = [
        'name', 'last_name', 'phone', 'photo'
    ];
    public function costumer(): HasOne
    {
        return $this->hasOne(Costumer::class, 'person_id', 'id');
    }
}
