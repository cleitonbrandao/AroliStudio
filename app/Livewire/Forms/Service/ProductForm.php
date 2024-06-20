<?php

namespace App\Livewire\Forms\Service;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product;
    #[Validate]
    public $name;
    public $price;
    public $cost_price;
    public $description;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                ValidationRule::exists('products', 'name'),
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
