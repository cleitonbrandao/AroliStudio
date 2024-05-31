<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';

    protected $casts = [
        'price' => MonetaryCorrency::class,
        'cost_price' => MonetaryCorrency::class
    ];
    protected $fillable = [
        'name', 'service_time', 'price', 'cost_price', 'description'
    ];
}
