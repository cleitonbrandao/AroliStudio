<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;
use NumberFormatter;

/**
 * Cast para valores monetários com suporte a múltiplos locales e precisão decimal.
 * 
 * BEST PRACTICES para valores monetários:
 * 
 * 1. BANCO DE DADOS: Use DECIMAL(15,3) - NOT NULL DEFAULT 0.000
 *    - DECIMAL garante precisão exata (vs FLOAT/DOUBLE que têm erros de arredondamento)
 *    - 15 dígitos totais: suficiente para valores até 999.999.999.999,999
 *    - 3 casas decimais: evita perda de precisão em cálculos (ex: divisões, juros)
 *    - Exemplo: CREATE TABLE products (price DECIMAL(15,3) NOT NULL DEFAULT 0.000)
 * 
 * 2. PHP/LARAVEL: Use string ou este Cast
 *    - Em cálculos: use BCMath (bcadd, bcmul) para precisão exata
 *    - Em exibição: use NumberFormatter ou Number::currency()
 *    - Evite float direto pois 0.1 + 0.2 != 0.3 em PHP
 * 
 * 3. MIGRATION: 
 *    $table->decimal('price', 15, 3)->default(0.000);
 * 
 * 4. MODEL CAST:
 *    protected $casts = ['price' => MonetaryCorrency::class];
 *    OU use a trait: use MonetaryCast;
 * 
 * @see https://dev.mysql.com/doc/refman/8.0/en/precision-math-decimal-characteristics.html
 */
class MonetaryCorrency implements CastsAttributes
{
    /**
     * Precisão decimal para armazenamento (3 casas para evitar perda em cálculos).
     */
    private const DECIMAL_PRECISION = 3;

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
     * Accessor: Formata o valor do banco para exibição visual.
     * 
     * Retorna string formatada como moeda no locale atual (ex: "R$ 1.234,56").
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value Valor float/decimal do banco
     * @param array<string, mixed> $attributes
     * @return string|null String formatada ou null se valor for null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        // Se moeda não for especificada, usa a da sessão ou padrão
        $currency = $this->currency ?? Session::get('currency', config('currency.default', 'BRL'));
        
        // Se locale não for especificado, usa o da aplicação
        $locale = $this->locale ?? App::getLocale();
        
        // Formata como moeda com parâmetros configuráveis
        return Number::currency($value, in: $currency, locale: $locale);
    }

    /**
     * Mutator: Converte valor de entrada (string formatada ou numérico) para float puro.
     * 
     * Suporta múltiplos formatos de entrada:
     * - Formato brasileiro: "1.234,56" ou "R$ 1.234,56"
     * - Formato americano: "1,234.56" ou "$1,234.56"
     * - Numérico direto: 1234.56 ou "1234.56"
     * - Valores negativos: "-1.234,56"
     * 
     * Usa NumberFormatter (ext-intl) para detectar locale automaticamente,
     * com fallback para parsing manual caso não consiga.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value Valor a ser convertido (string, int, float)
     * @param array<string, mixed> $attributes
     * @return float|null Float com 3 casas decimais ou null se inválido
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        // Null ou vazio retorna null
        if ($value === null || $value === '') {
            return null;
        }

        // Se já for numérico puro, retorna com precisão correta
        if (is_numeric($value)) {
            return round((float)$value, self::DECIMAL_PRECISION);
        }

        // Primeiro tenta parsing manual (mais confiável para formatos com separadores)
        $parsed = $this->parseManually($value);
        if ($parsed !== null) {
            return round($parsed, self::DECIMAL_PRECISION);
        }

        // Fallback: tenta com NumberFormatter se manual falhou
        $parsed = $this->parseWithNumberFormatter($value);
        if ($parsed !== null) {
            return round($parsed, self::DECIMAL_PRECISION);
        }

        // Se não conseguiu parsear, loga warning e retorna null
        Log::warning("MonetaryCorrency: Não foi possível parsear valor monetário", [
            'value' => $value,
            'model' => get_class($model),
            'attribute' => $key
        ]);

        return null;
    }

    /**
     * Tenta parsear o valor usando NumberFormatter com múltiplos locales.
     * 
     * Primeiro tenta com o locale da aplicação, depois tenta pt_BR e en_US.
     *
     * @param mixed $value Valor string a ser parseado
     * @return float|null Float parseado ou null se falhou
     */
    private function parseWithNumberFormatter(mixed $value): ?float
    {
        if (!extension_loaded('intl')) {
            return null;
        }

        $stringValue = trim((string)$value);
        
        // Lista de locales para tentar (prioriza locale da aplicação)
        $locales = [
            $this->locale ?? App::getLocale(),
            'pt_BR',
            'en_US',
        ];

        foreach ($locales as $locale) {
            try {
                $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
                $parsed = $formatter->parse($stringValue, NumberFormatter::TYPE_DOUBLE);
                
                // Verifica se o parsing foi bem-sucedido
                if ($parsed !== false && is_numeric($parsed)) {
                    return (float)$parsed;
                }
            } catch (\Exception $e) {
                // Continua para o próximo locale
                continue;
            }
        }

        return null;
    }

    /**
     * Parsing manual para formatos brasileiros e americanos.
     * 
     * Detecta o formato baseado na posição dos separadores.
     *
     * @param mixed $value Valor string a ser parseado
     * @return float|null Float parseado ou null se inválido
     */
    private function parseManually(mixed $value): ?float
    {
        $stringValue = trim((string)$value);
        
        // Remove espaços e símbolos de moeda (R$, $, €, etc.)
        $cleanValue = preg_replace('/[^\d.,\-+]/', '', $stringValue);
        
        if (empty($cleanValue) || $cleanValue === '-' || $cleanValue === '+') {
            return null;
        }

        // Detecta formato baseado na posição dos separadores
        $lastComma = strrpos($cleanValue, ',');
        $lastDot = strrpos($cleanValue, '.');

        if ($lastComma !== false && $lastDot !== false) {
            // Ambos presentes - último é o decimal
            if ($lastComma > $lastDot) {
                // Formato brasileiro: 1.234.567,89
                $cleanValue = str_replace('.', '', $cleanValue);
                $cleanValue = str_replace(',', '.', $cleanValue);
            } else {
                // Formato americano: 1,234,567.89
                $cleanValue = str_replace(',', '', $cleanValue);
            }
        } elseif ($lastComma !== false) {
            // Só vírgula: verifica se é decimal ou separador de milhares
            $afterComma = substr($cleanValue, $lastComma + 1);
            $beforeComma = substr($cleanValue, 0, $lastComma);
            $commaCount = substr_count($cleanValue, ',');
            
            // É separador de milhares americano APENAS se:
            // 1. Tem exatamente 3 dígitos após a vírgula E
            // 2. Valor antes da vírgula >= 1000 (formato 1,234 significa mil e duzentos)
            // Caso contrário, assume decimal brasileiro
            $isAmericanThousands = $commaCount === 1 
                                    && strlen($afterComma) === 3 
                                    && strlen($beforeComma) >= 1 
                                    && floatval($beforeComma) >= 1000;
            
            if ($isAmericanThousands) {
                // Separador de milhares americano (1,234 onde 1 >= 1000 não faz sentido, mas 1000,234 sim)
                $cleanValue = str_replace(',', '', $cleanValue);
            } else {
                // Decimal brasileiro (0,001 ou 12,345 ou 10,12345 ou 1,234 onde 1 < 1000)
                $cleanValue = str_replace(',', '.', $cleanValue);
            }
        }
        // Se só houver ponto, assume formato americano (já está correto)

        // Valida se o resultado é numérico
        if (!is_numeric($cleanValue)) {
            return null;
        }

        return (float)$cleanValue;
    }

    /**
     * Serializa o valor para array/JSON.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return float|null
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        return $value !== null ? round((float)$value, self::DECIMAL_PRECISION) : null;
    }
}
