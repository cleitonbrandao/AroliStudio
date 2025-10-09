<?php

namespace App\Livewire\Forms\Service;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product;
    #[Validate(rule: 'required', message: 'Obrigatorio um nome de prouto.')]
    #[Validate(rule: 'unique:products', attribute: 'name', message: 'Produto já cadastrado.')]
    public $name;
    public $price;
    public $cost_price;
    public $description;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('products', 'name')->ignore($this->product->id),
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
    }

    public function store()
    {
        $this->validate();
        Product::create($this->all());
    }

    public function update(): void
    {
        $this->validate();

        $this->product->update(
            $this->all()
        );
    }
}
