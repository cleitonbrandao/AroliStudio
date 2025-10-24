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
        if (empty($value)) {
            return null;
        }

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
        if (empty($value)) {
            return null;
        }

        // Tenta detectar o formato e converter
        // Formato brasileiro: d/m/Y ou d-m-Y
        if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $value)) {
            return Carbon::createFromFormat('d/m/Y', str_replace('-', '/', $value))->format('Y-m-d');
        }
        
        // Formato ISO: Y-m-d (do input HTML5 date)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return $value; // Já está no formato correto para o banco
        }

        // Fallback: tenta criar a partir de qualquer formato
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
