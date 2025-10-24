<?php

namespace App\Casts;

use App\Support\MoneyWrapper;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

/**
 * Custom Cast Eloquent para valores monetários usando Brick\Money.
 * 
 * INSTALAÇÃO:
 * composer require brick/money
 * 
 * POR QUÊ BRICK/MONEY?
 * - Precisão absoluta: usa BCMath internamente (sem erros de float)
 * - Imutabilidade: operações retornam novas instâncias (seguro)
 * - Type-safe: garante que moeda e valor estão sempre consistentes
 * - Formatação embutida: suporta múltiplos locales e formatos
 * - Conversão de moedas: suporte nativo (quando configurado)
 * - Aritmética monetária: add(), subtract(), multiply(), divide() com precisão
 * 
 * MIGRATION RECOMENDADA:
 * ```php
 * Schema::create('products', function (Blueprint $table) {
 *     $table->id();
 *     $table->decimal('price', 15, 3)->comment('Valor monetário com 3 casas decimais');
 *     $table->string('currency', 3)->default('BRL')->comment('Código ISO 4217 (BRL, USD, EUR)');
 *     $table->timestamps();
 * });
 * ```
 * 
 * USO NO MODEL:
 * ```php
 * protected $casts = [
 *     'price' => MonetaryCurrency::class,
 * ];
 * 
 * // Opcionalmente, defina a coluna de moeda (padrão: 'currency')
 * protected $currencyColumn = 'currency'; // ou 'moeda', 'coin', etc.
 * ```
 * 
 * EXEMPLOS DE USO:
 * ```php
 * // Criar/atualizar
 * $product = Product::create([
 *     'price' => Money::of(1234.567, 'BRL'),
 *     // ou simplesmente:
 *     'price' => 1234.567,
 *     'currency' => 'BRL',
 * ]);
 * 
 * // Ler (retorna Money instance)
 * $price = $product->price; // Money instance
 * echo $price->getAmount();        // "1234.567"
 * echo $price->getCurrency();      // "BRL"
 * echo $price->formatTo('pt_BR');  // "R$ 1.234,57" (arredonda para 2 na exibição)
 * 
 * // Cálculos (type-safe e preciso)
 * $discount = $product->price->multipliedBy(0.1, RoundingMode::DOWN);
 * $final = $product->price->minus($discount);
 * 
 * // Comparações
 * if ($product->price->isGreaterThan(Money::of(1000, 'BRL'))) {
 *     // ...
 * }
 * ```
 * 
 * BEST PRACTICES:
 * - Use DECIMAL(15,3) no banco (nunca FLOAT/DOUBLE)
 * - Sempre especifique a moeda (BRL, USD, EUR)
 * - Use RoundingMode::HALF_UP para arredondamentos comerciais
 * - Para exibição, use formatTo() ou getFormatted()
 * - Para cálculos, use os métodos do Money (plus, minus, multipliedBy, dividedBy)
 * 
 * @see https://github.com/brick/money
 * @see https://www.iso.org/iso-4217-currency-codes.html
 */
class MonetaryCurrency implements CastsAttributes
{
    /**
     * Precisão decimal para armazenamento (3 casas para cálculos precisos).
     */
    private const DECIMAL_PRECISION = 3;

    /**
     * Moeda padrão (código ISO 4217).
     */
    private const DEFAULT_CURRENCY = 'BRL';

    /**
     * Nome da coluna que armazena o código da moeda no banco.
     */
    private const DEFAULT_CURRENCY_COLUMN = 'currency';

    /**
     * @param string|null $currencyCode Código ISO da moeda (BRL, USD, EUR) - Se null, busca do model
     * @param string|null $currencyColumn Nome da coluna que contém o código da moeda
     */
    public function __construct(
        protected ?string $currencyCode = null,
        protected ?string $currencyColumn = null
    ) {
        $this->currencyColumn ??= self::DEFAULT_CURRENCY_COLUMN;
    }

    /**
     * Accessor: Converte valor DECIMAL do banco em instância MoneyWrapper.
     * 
     * Retorna um objeto MoneyWrapper com helpers convenientes:
     * - ->formatted() : Formatado com símbolo e 2 casas decimais
     * - ->toDecimal() : Apenas o valor com 2 casas decimais
     * - ->toLocalizedDecimal() : Valor formatado sem símbolo
     * - ->getCurrencyCode() : Código da moeda (BRL, USD)
     * - ->getCurrencySymbol() : Símbolo (R$, $)
     *
     * @param Model $model Instância do model
     * @param string $key Nome do atributo
     * @param mixed $value Valor DECIMAL do banco (string: "1234.567")
     * @param array<string, mixed> $attributes Todos os atributos do model
     * @return MoneyWrapper|null Instância MoneyWrapper ou null se valor for null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MoneyWrapper
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Determina a moeda (prioridade: parâmetro > coluna model > padrão)
            $currency = $this->resolveCurrency($model, $attributes);

            // Cria contexto monetário com 3 casas decimais
            $context = new CustomContext(self::DECIMAL_PRECISION);
            
            // Cria instância Money preservando 3 casas decimais
            $money = Money::of($value, $currency, $context, RoundingMode::HALF_UP);
            
            // Retorna wrapped para facilitar uso
            return new MoneyWrapper($money);
        } catch (\Throwable $e) {
            Log::error("MonetaryCurrency: Erro ao criar Money instance", [
                'value' => $value,
                'currency' => $currency ?? 'unknown',
                'model' => get_class($model),
                'attribute' => $key,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Mutator: Converte Money/MoneyWrapper ou valor numérico para string DECIMAL.
     * 
     * Aceita:
     * - Instância de MoneyWrapper (extrai o Money interno)
     * - Instância de Money (extrai o amount)
     * - Valores numéricos (int, float, string numérica)
     * - Null/vazio (retorna null)
     *
     * @param Model $model Instância do model
     * @param string $key Nome do atributo
     * @param mixed $value Valor a ser armazenado (MoneyWrapper, Money, numeric, null)
     * @param array<string, mixed> $attributes Todos os atributos do model
     * @return string|null String decimal "1234.567" ou null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Null ou vazio retorna null
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Se for MoneyWrapper, extrai o Money interno
            if ($value instanceof MoneyWrapper) {
                $value = $value->getMoney();
            }
            
            // Se for uma instância de Money, extrai o amount
            if ($value instanceof Money) {
                // Opcionalmente, atualiza a coluna de moeda no model
                $this->updateCurrencyColumn($model, $value->getCurrency()->getCurrencyCode());
                
                // Converte para string com 3 casas decimais
                return $value->getAmount()->toScale(self::DECIMAL_PRECISION, RoundingMode::HALF_UP);
            }

            // Se for numérico (int, float, string), converte para decimal string
            if (is_numeric($value)) {
                return number_format(
                    (float)$value,
                    self::DECIMAL_PRECISION,
                    '.',
                    ''
                );
            }

            // Tenta criar Money a partir de string formatada (ex: "R$ 1.234,56")
            $parsed = $this->parseFormattedValue($value);
            if ($parsed !== null) {
                return number_format(
                    $parsed,
                    self::DECIMAL_PRECISION,
                    '.',
                    ''
                );
            }

            // Valor inválido
            Log::warning("MonetaryCurrency: Não foi possível converter valor para Money", [
                'value' => $value,
                'type' => get_debug_type($value),
                'model' => get_class($model),
                'attribute' => $key,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error("MonetaryCurrency: Erro ao processar valor monetário", [
                'value' => $value,
                'model' => get_class($model),
                'attribute' => $key,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Serializa o valor para array/JSON.
     * 
     * Retorna um array com amount (2 casas decimais), currency, formatted e symbol.
     *
     * @param Model $model Instância do model
     * @param string $key Nome do atributo
     * @param MoneyWrapper|null $value Instância MoneyWrapper
     * @param array<string, mixed> $attributes Todos os atributos do model
     * @return array<string, mixed>|null Array com dados formatados ou null
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if (!$value instanceof MoneyWrapper) {
            return null;
        }

        return $value->toArray();
    }

    /**
     * Resolve o código da moeda a ser usado.
     * 
     * Ordem de prioridade:
     * 1. Parâmetro do construtor ($currencyCode)
     * 2. Valor da coluna de moeda no model
     * 3. Moeda padrão (BRL)
     *
     * @param Model $model Instância do model
     * @param array<string, mixed> $attributes Atributos do model
     * @return string Código ISO da moeda (ex: 'BRL')
     */
    private function resolveCurrency(Model $model, array $attributes): string
    {
        // 1. Parâmetro explícito
        if ($this->currencyCode !== null) {
            return strtoupper($this->currencyCode);
        }

        // 2. Coluna do model
        if (isset($attributes[$this->currencyColumn])) {
            return strtoupper($attributes[$this->currencyColumn]);
        }

        // 3. Propriedade do model (se existir)
        if (property_exists($model, 'currency') && !empty($model->currency)) {
            return strtoupper($model->currency);
        }

        // 4. Padrão
        return self::DEFAULT_CURRENCY;
    }

    /**
     * Atualiza a coluna de moeda no model (se existir).
     *
     * @param Model $model Instância do model
     * @param string $currencyCode Código ISO da moeda
     * @return void
     */
    private function updateCurrencyColumn(Model $model, string $currencyCode): void
    {
        // Só atualiza se a coluna existir no model e for fillable/guarded
        if (
            $model->hasAttribute($this->currencyColumn) ||
            in_array($this->currencyColumn, $model->getFillable(), true)
        ) {
            $model->setAttribute($this->currencyColumn, strtoupper($currencyCode));
        }
    }

    /**
     * Tenta parsear valor formatado (ex: "R$ 1.234,56") para float.
     *
     * @param mixed $value Valor string formatado
     * @return float|null Float parseado ou null se inválido
     */
    private function parseFormattedValue(mixed $value): ?float
    {
        if (!is_string($value)) {
            return null;
        }

        $stringValue = trim($value);

        // Remove símbolos de moeda e espaços
        $cleanValue = preg_replace('/[^\d.,\-+]/', '', $stringValue);

        if (empty($cleanValue)) {
            return null;
        }

        // Detecta formato baseado na posição dos separadores
        $lastComma = strrpos($cleanValue, ',');
        $lastDot = strrpos($cleanValue, '.');

        if ($lastComma !== false && $lastDot !== false) {
            // Ambos presentes - último é o decimal
            if ($lastComma > $lastDot) {
                // Formato brasileiro: 1.234,56
                $cleanValue = str_replace('.', '', $cleanValue);
                $cleanValue = str_replace(',', '.', $cleanValue);
            } else {
                // Formato americano: 1,234.56
                $cleanValue = str_replace(',', '', $cleanValue);
            }
        } elseif ($lastComma !== false) {
            // Só vírgula: assume decimal brasileiro
            $cleanValue = str_replace(',', '.', $cleanValue);
        }

        return is_numeric($cleanValue) ? (float)$cleanValue : null;
    }

    /**
     * Formata Money para exibição visual usando locale da aplicação.
     *
     * @param Money $money Instância Money
     * @return string String formatada (ex: "R$ 1.234,57")
     */
    private function formatMoney(Money $money): string
    {
        $locale = App::getLocale();

        try {
            // Usa formatTo do Brick\Money (requer ext-intl)
            return $money->formatTo($locale);
        } catch (\Throwable $e) {
            // Fallback: formatação manual
            return $this->manualFormat($money, $locale);
        }
    }

    /**
     * Formatação manual quando ext-intl não está disponível.
     *
     * @param Money $money Instância Money
     * @param string $locale Locale (ex: 'pt_BR')
     * @return string String formatada
     */
    private function manualFormat(Money $money, string $locale): string
    {
        $amount = $money->getAmount()->toFloat();
        $currencyCode = $money->getCurrency()->getCurrencyCode();

        // Mapeamento simples de símbolos
        $symbols = [
            'BRL' => 'R$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$currencyCode] ?? $currencyCode;

        // Formatação baseada em locale
        if (str_starts_with($locale, 'pt')) {
            // Formato brasileiro
            return sprintf(
                '%s %s',
                $symbol,
                number_format($amount, 2, ',', '.')
            );
        }

        // Formato americano/internacional
        return sprintf(
            '%s%s',
            $symbol,
            number_format($amount, 2, '.', ',')
        );
    }
}
