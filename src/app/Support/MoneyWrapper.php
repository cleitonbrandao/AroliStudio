<?php

namespace App\Support;

use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * Wrapper para Brick\Money com helpers para facilitar uso no Laravel.
 * 
 * Adiciona métodos convenientes para formatação e conversão,
 * respeitando locale e configurações da aplicação.
 */
class MoneyWrapper
{
    protected Money $money;

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    /**
     * Retorna a instância Money original.
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * Formata para exibição com 2 casas decimais, usando locale do usuário.
     * 
     * O locale determina:
     * - Formato dos números (1.234,57 vs 1,234.57)
     * - Posição do símbolo (R$ 1.234,57 vs $1,234.57)
     * - Separadores de milhares e decimais
     * 
     * @param string|null $locale Locale customizado (pt_BR, en_US, etc.)
     *                            Se null, usa App::getLocale() (definido pelo usuário)
     * @return string Valor formatado (ex: "R$ 1.234,57" ou "$1,234.57")
     */
    public function formatted(?string $locale = null): string
    {
        // Usa locale do usuário (da sessão via SetLocale middleware)
        $locale = $locale ?? App::getLocale();
        
        try {
            // Arredonda para 2 casas decimais para exibição
            $rounded = $this->money->getAmount()->toScale(2, RoundingMode::HALF_UP);
            $moneyRounded = Money::of($rounded, $this->money->getCurrency(), new CustomContext(2));
            
            // brick/money usa ext-intl para formatação correta do locale
            return $moneyRounded->formatTo($locale);
        } catch (\Throwable $e) {
            // Fallback manual se ext-intl não estiver disponível
            return $this->manualFormat($locale);
        }
    }

    /**
     * Retorna apenas o valor numérico com 2 casas decimais (sem símbolo de moeda).
     * Útil para inputs de formulário.
     * 
     * @return string Valor decimal (ex: "1234.57")
     */
    public function toDecimal(): string
    {
        return $this->money->getAmount()->toScale(2, RoundingMode::HALF_UP);
    }

    /**
     * Retorna valor com 2 casas decimais no formato do locale.
     * Útil para inputs masked/formatados.
     * 
     * @param string|null $locale
     * @return string Valor formatado sem símbolo (ex: "1.234,57" ou "1,234.57")
     */
    public function toLocalizedDecimal(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $decimal = $this->toDecimal();
        
        // Formato brasileiro
        if (str_starts_with($locale, 'pt')) {
            return number_format((float)$decimal, 2, ',', '.');
        }
        
        // Formato americano/internacional
        return number_format((float)$decimal, 2, '.', ',');
    }

    /**
     * Retorna o código da moeda (BRL, USD, EUR).
     */
    public function getCurrencyCode(): string
    {
        return $this->money->getCurrency()->getCurrencyCode();
    }

    /**
     * Retorna o símbolo da moeda (R$, $, €).
     */
    public function getCurrencySymbol(): string
    {
        $symbols = [
            'BRL' => 'R$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
        ];

        return $symbols[$this->getCurrencyCode()] ?? $this->getCurrencyCode();
    }

    /**
     * Retorna array para JSON/API.
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->toDecimal(),
            'currency' => $this->getCurrencyCode(),
            'formatted' => $this->formatted(),
            'symbol' => $this->getCurrencySymbol(),
        ];
    }

    /**
     * Converte para string (usa formatação automática).
     */
    public function __toString(): string
    {
        return $this->formatted();
    }

    /**
     * Permite acessar métodos do Money original.
     */
    public function __call(string $method, array $arguments)
    {
        $result = $this->money->$method(...$arguments);
        
        // Se o resultado for Money, retorna wrapped
        if ($result instanceof Money) {
            return new self($result);
        }
        
        return $result;
    }

    /**
     * Formatação manual quando ext-intl não está disponível.
     */
    private function manualFormat(string $locale): string
    {
        $amount = (float)$this->toDecimal();
        $symbol = $this->getCurrencySymbol();

        // Formato brasileiro
        if (str_starts_with($locale, 'pt')) {
            return sprintf('%s %s', $symbol, number_format($amount, 2, ',', '.'));
        }

        // Formato americano/internacional
        return sprintf('%s%s', $symbol, number_format($amount, 2, '.', ','));
    }

    /**
     * Factory method: cria MoneyWrapper a partir de valores diversos.
     */
    public static function make(mixed $value, string $currency = 'BRL'): self
    {
        if ($value instanceof Money) {
            return new self($value);
        }

        if ($value instanceof self) {
            return $value;
        }

        $context = new CustomContext(3);
        $money = Money::of($value, $currency, $context);
        
        return new self($money);
    }
}
