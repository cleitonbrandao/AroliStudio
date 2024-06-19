<?php

namespace App\Livewire\Components\Service;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class ProductsForm extends Component
{
    public ?Product $product;

    public function render(): View
    {
        return view('livewire.components.service.products-form');
    }
}
