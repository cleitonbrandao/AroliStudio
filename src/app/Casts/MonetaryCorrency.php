<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;

class MonetaryCorrency implements CastsAttributes
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
        // Se já for numérico, retorna formatado
        if (is_numeric($value)) {
            return number_format((float)$value, 2, '.', '');
        }
        
        // Remove espaços e símbolos de moeda
        $cleanValue = trim($value);
        $cleanValue = preg_replace('/[^\d.,\-]/', '', $cleanValue);
        
        // Detecta o formato baseado na posição dos separadores
        // Se houver vírgula após o ponto, é formato brasileiro (1.234,56)
        // Se houver ponto após a vírgula, é formato americano (1,234.56)
        $lastComma = strrpos($cleanValue, ',');
        $lastDot = strrpos($cleanValue, '.');
        
        if ($lastComma !== false && $lastDot !== false) {
            // Ambos presentes - determinar qual é o separador decimal
            if ($lastComma > $lastDot) {
                // Formato brasileiro: 1.234,56
                $cleanValue = str_replace('.', '', $cleanValue); // Remove separador de milhares
                $cleanValue = str_replace(',', '.', $cleanValue); // Vírgula vira ponto decimal
            } else {
                // Formato americano: 1,234.56
                $cleanValue = str_replace(',', '', $cleanValue); // Remove separador de milhares
            }
        } elseif ($lastComma !== false) {
            // Só vírgula presente - pode ser decimal brasileiro ou milhar americano
            // Assume decimal se houver apenas uma vírgula e 2 dígitos depois
            if (substr_count($cleanValue, ',') === 1 && preg_match('/,\d{1,2}$/', $cleanValue)) {
                $cleanValue = str_replace(',', '.', $cleanValue);
            } else {
                $cleanValue = str_replace(',', '', $cleanValue);
            }
        }
        // Se só houver ponto, mantém como está (formato americano)
        
        return number_format((float)$cleanValue, 2, '.', '');
    }
}
