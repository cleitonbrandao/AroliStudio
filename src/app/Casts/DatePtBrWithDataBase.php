<?php

namespace App\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;

class DatePtBrWithDataBase implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        Carbon::setLocale('pt_BR');
        return Carbon::createFromDate($value)
            ->setTimezone('America/Sao_Paulo')
            ->isoFormat('dddd, D [de] MMMM [de] YYYY');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}
