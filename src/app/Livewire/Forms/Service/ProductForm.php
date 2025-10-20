<?php

namespace App\Livewire\Forms\Service;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;
    
    #[Validate(rule: 'required', message: 'Obrigatorio um nome de prouto.')]
    public $name;
    public $price;
    public $cost_price;
    public $description;
    
    #[Locked] // Previne manipulação via DevTools/Livewire Inspector
    public $team_id;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('products', 'name')->ignore($this->product?->id),
                'min:4',
                'string'
            ],
            'price' => [
                'nullable',
            ],
            'cost_price' => [
                'nullable'
            ],
            'description' => [
                'nullable',
                'string'
            ]
        ];
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        
        // Converte para formato decimal do locale atual para exibição no input
        $locale = app()->getLocale();
        $isPortuguese = str_starts_with($locale, 'pt');
        
        // Obtém o valor decimal (ex: "1234.57")
        $priceDecimal = $product->price?->toDecimal();
        $costDecimal = $product->cost_price?->toDecimal();
        
        // Formata para o locale do usuário
        // number_format já retorna no formato correto: '1.234,57' (pt) ou '1,234.57' (en)
        if ($priceDecimal) {
            $this->price = $isPortuguese 
                ? number_format((float)$priceDecimal, 2, ',', '.')
                : number_format((float)$priceDecimal, 2, '.', ',');
        }
        
        if ($costDecimal) {
            $this->cost_price = $isPortuguese
                ? number_format((float)$costDecimal, 2, ',', '.')
                : number_format((float)$costDecimal, 2, '.', ',');
        }
        
        $this->description = $product->description;
        $this->team_id = $product->team_id; // Preserva o team_id ao editar
    }

    public function store()
    {
        // Valida os dados
        $this->validate();
        
        // SEGURANÇA: Força o team_id a ser sempre do currentTeam do usuário autenticado
        // Isso previne manipulação via DevTools/Inspector do Livewire
        $this->team_id = Auth::user()->currentTeam->id;
        
        // Cria o produto com os dados validados
        Product::create([
            'name' => $this->name,
            'price' => $this->sanitizeMoneyValue($this->price),
            'cost_price' => $this->sanitizeMoneyValue($this->cost_price),
            'description' => $this->description,
            'team_id' => $this->team_id,
        ]);
    }

    public function update(): void
    {
        $this->validate();
        
        // SEGURANÇA: Verifica se o produto pertence ao currentTeam do usuário
        if ($this->product->team_id !== Auth::user()->currentTeam->id) {
            throw new \Exception('Você não tem permissão para editar este produto.');
        }

        $this->product->update([
            'name' => $this->name,
            'price' => $this->sanitizeMoneyValue($this->price),
            'cost_price' => $this->sanitizeMoneyValue($this->cost_price),
            'description' => $this->description,
            // team_id não pode ser alterado na edição (protege contra manipulação)
        ]);
    }

    /**
     * Sanitiza valor monetário para formato decimal.
     * Remove formatação de locale e garante que seja um decimal válido.
     * 
     * @param mixed $value Valor que pode estar formatado (1.234,56 ou 1,234.56)
     * @return string|null Valor decimal (1234.56) ou null
     */
    private function sanitizeMoneyValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Se já for numérico simples (ex: "1234.56"), retorna direto
        if (is_numeric($value)) {
            return (string) $value;
        }

        // Remove espaços
        $value = trim($value);
        
        // Remove símbolos de moeda (R$, $, €, etc)
        $value = preg_replace('/[R\$€£¥\s]/u', '', $value);
        
        // Detecta o formato baseado nos separadores
        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');
        
        // Ambos presentes
        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                // Formato brasileiro: 1.234,56 → 1234.56
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                // Formato americano: 1,234.56 → 1234.56
                $value = str_replace(',', '', $value);
            }
        } 
        // Apenas vírgula
        elseif ($lastComma !== false) {
            // Assume formato brasileiro: 1234,56 → 1234.56
            $value = str_replace(',', '.', $value);
        }
        // Apenas ponto ou nada - já está no formato correto
        
        // Garante que é numérico
        return is_numeric($value) ? (string) $value : null;
    }
}
