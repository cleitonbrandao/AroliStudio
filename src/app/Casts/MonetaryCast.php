<?php

namespace App\Casts;

use Brick\Money\Money;

/**
 * Trait para aplicar automaticamente o cast MonetaryCurrency em atributos monetários.
 * 
 * COMO USAR:
 * 
 * 1. Adicione a trait no model:
 * 
 *    use App\Casts\MonetaryCast;
 *    
 *    class Product extends Model
 *    {
 *        use MonetaryCast;
 *        
 *        protected array $monetaryAttributes = ['price', 'cost_price', 'discount'];
 *    }
 * 
 * 2. Defina quais atributos são monetários no array $monetaryAttributes
 * 
 * 3. A trait aplicará automaticamente o cast MonetaryCurrency usando brick/money
 * 
 * 4. OPCIONAL: Para configuração customizada por atributo:
 * 
 *    protected array $monetaryAttributes = [
 *        'price' => ['currency' => 'USD'],
 *        'cost_price' => [], // Usa padrões da sessão
 *    ];
 * 
 * MIGRAÇÃO RECOMENDADA:
 * 
 *    Schema::create('products', function (Blueprint $table) {
 *        $table->id();
 *        $table->decimal('price', 15, 3)->default(0.000)->comment('Preço de venda');
 *        $table->string('currency', 3)->default('BRL')->comment('Código ISO da moeda');
 *        $table->decimal('cost_price', 15, 3)->default(0.000)->comment('Preço de custo');
 *        $table->timestamps();
 *    });
 */
trait MonetaryCast
{
    /**
     * Boot da trait: aplica casts monetários automaticamente.
     *
     * @return void
     */
    public static function bootMonetaryCast(): void
    {
        // Não precisa fazer nada no boot - os casts são aplicados via initializeMonetaryCast
    }

    /**
     * Inicializa a trait e configura os casts monetários.
     *
     * @return void
     */
    public function initializeMonetaryCast(): void
    {
        if (!property_exists($this, 'monetaryAttributes')) {
            return;
        }

        // Garante que $casts existe
        if (!property_exists($this, 'casts')) {
            $this->casts = [];
        }

        // Aplica MonetaryCurrency para cada atributo monetário
        foreach ($this->monetaryAttributes as $key => $config) {
            // Se for array indexado: ['price', 'cost'] → usa config padrão
            if (is_numeric($key)) {
                $attribute = $config;
                $this->casts[$attribute] = MonetaryCurrency::class;
            } 
            // Se for array associativo: ['price' => ['currency' => 'USD']] → usa config customizada
            else {
                $attribute = $key;
                $currency = $config['currency'] ?? null;
                $currencyColumn = $config['currencyColumn'] ?? null;
                
                // Cria cast com parâmetros customizados
                $this->casts[$attribute] = MonetaryCurrency::class . ($currency || $currencyColumn ? ":$currency,$currencyColumn" : '');
            }
        }
    }

    /**
     * Helper para formatar múltiplos atributos monetários de uma vez.
     * 
     * Útil para exibir valores em views sem chamar get() manualmente.
     * Retorna Money objects que podem ser formatados com formatTo().
     *
     * @param array<string> $attributes Lista de atributos para formatar
     * @return array<string, Money|null> Array associativo [atributo => Money_instance]
     */
    public function formatMonetary(array $attributes = []): array
    {
        // Se não passar atributos, usa todos os monetaryAttributes
        if (empty($attributes)) {
            $attributes = is_array($this->monetaryAttributes) 
                ? (array_is_list($this->monetaryAttributes) ? $this->monetaryAttributes : array_keys($this->monetaryAttributes))
                : [];
        }

        $formatted = [];
        foreach ($attributes as $attribute) {
            // Retorna Money instance (não string formatada)
            $formatted[$attribute] = $this->getAttribute($attribute);
        }

        return $formatted;
    }

    /**
     * Helper para obter o valor raw (string decimal) sem formatação.
     * 
     * Útil para cálculos onde você precisa do decimal puro do banco.
     *
     * @param string $attribute Nome do atributo
     * @return string|null Valor decimal ou null
     */
    public function getRawMonetary(string $attribute): ?string
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * Helper para setar valor monetário de forma explícita.
     * 
     * Passa pelo cast set() para garantir parsing correto.
     * Aceita Money instance, valores numéricos ou strings formatadas.
     *
     * @param string $attribute Nome do atributo
     * @param mixed $value Valor a setar (Money, string formatada ou numérico)
     * @return self
     */
    public function setMonetary(string $attribute, mixed $value): self
    {
        $this->setAttribute($attribute, $value);
        return $this;
    }
}
