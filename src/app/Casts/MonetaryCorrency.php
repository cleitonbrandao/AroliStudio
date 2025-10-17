<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;

class MonetaryCurrency implements CastsAttributes
{
    /**
     * @param string|null $currency Código da moeda (BRL, USD, EUR, etc.) - Se null, usa da sessão
     * @param string|null $locale Locale para formatação (pt_BR, en_US, etc.) - Se null, usa da aplicação
     */
    public function __construct(
        protected ?string $currency = null,
        protected ?string $locale = null
    ) {
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Se moeda não for especificada, usa a da sessão ou padrão
        $currency = $this->currency ?? Session::get('currency', config('currency.default'));
        
        // Se locale não for especificado, usa o da aplicação
        $locale = $this->locale ?? App::getLocale();
        
        // Formata como moeda com parâmetros configuráveis
        return Number::currency($value, in: $currency, locale: $locale);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Remove formatação e salva apenas o número
        $cleanValue = preg_replace('/[^\d.,]/', '', $value);
        $cleanValue = str_replace(',', '.', $cleanValue);
        return number_format((float)$cleanValue, 2, '.', '');
    }
}
