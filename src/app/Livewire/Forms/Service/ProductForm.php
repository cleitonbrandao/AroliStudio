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
        $this->price = $product->price;
        $this->cost_price = $product->cost_price;
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
            'price' => $this->price,
            'cost_price' => $this->cost_price,
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
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'description' => $this->description,
            // team_id não pode ser alterado na edição (protege contra manipulação)
        ]);
    }
}
