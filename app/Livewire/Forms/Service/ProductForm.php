<?php

namespace App\Livewire\Forms\Service;

use App\Models\Product;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product;

    public string $name;
    public $price;
    public $cost_price;
    public string $description;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('products')->ignore($this->name),
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
}
