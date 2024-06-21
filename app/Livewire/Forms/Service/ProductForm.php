<?php

namespace App\Livewire\Forms\Service;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product;
    #[Validate('required', 'name', message: 'Nome de Produto Obrigatorio.')]
    public $name;
    public $price;
    public $cost_price;
    public $description;

    public function rules(): array
    {
        return [
            'name' => [
                Rule::unique('products', 'name')->ignore($this->product->id),
                'required',
                'min:4',
                'string'
            ],
            'price' => [
                'nullable'
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
    }

    public function update()
    {
        $this->validate();
        $this->product->update(
            $this->all()
        );
    }
}
