<?php

namespace App\Casts;

/**
 * Trait para aplicar automaticamente o cast MonetaryCorrency em atributos monetários.
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
 * 3. A trait aplicará automaticamente o cast MonetaryCorrency com locale/moeda da sessão
 * 
 * 4. OPCIONAL: Para configuração customizada por atributo:
 * 
 *    protected array $monetaryAttributes = [
 *        'price' => ['currency' => 'USD', 'locale' => 'en_US'],
 *        'cost_price' => [], // Usa padrões da sessão
 *    ];
 * 
 * MIGRAÇÃO RECOMENDADA:
 * 
 *    Schema::create('products', function (Blueprint $table) {
 *        $table->id();
 *        $table->decimal('price', 15, 3)->default(0.000)->comment('Preço de venda');
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

        // Aplica MonetaryCorrency para cada atributo monetário
        foreach ($this->monetaryAttributes as $key => $config) {
            // Se for array indexado: ['price', 'cost'] → usa config padrão
            if (is_numeric($key)) {
                $attribute = $config;
                $this->casts[$attribute] = MonetaryCorrency::class;
            } 
            // Se for array associativo: ['price' => ['currency' => 'USD']] → usa config customizada
            else {
                $attribute = $key;
                $currency = $config['currency'] ?? null;
                $locale = $config['locale'] ?? null;
                
                // Cria cast com parâmetros customizados
                $this->casts[$attribute] = MonetaryCorrency::class . ($currency || $locale ? ":$currency,$locale" : '');
            }
        }
    }

    /**
     * Helper para formatar múltiplos atributos monetários de uma vez.
     * 
     * Útil para exibir valores em views sem chamar get() manualmente.
     *
     * @param array<string> $attributes Lista de atributos para formatar
     * @return array<string, string|null> Array associativo [atributo => valor_formatado]
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
            // Usa o cast get() para formatar
            $formatted[$attribute] = $this->getAttribute($attribute);
        }

        return $formatted;
    }

    /**
     * Helper para obter o valor raw (float) sem formatação.
     * 
     * Útil para cálculos onde você precisa do float puro.
     *
     * @param string $attribute Nome do atributo
     * @return float|null Valor float ou null
     */
    public function getRawMonetary(string $attribute): ?float
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * Helper para setar valor monetário de forma explícita.
     * 
     * Passa pelo cast set() para garantir parsing correto.
     *
     * @param string $attribute Nome do atributo
     * @param mixed $value Valor a setar (string formatada ou numérico)
     * @return self
     */
    public function setMonetary(string $attribute, mixed $value): self
    {
        $this->setAttribute($attribute, $value);
        return $this;
    }
}
