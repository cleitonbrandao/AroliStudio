<?php

namespace App\Livewire\Components\Service;

use App\Livewire\Forms\Service\ProductForm;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class ProductsForm extends Component
{
    public ProductForm $productForm;

    public function mount(Product $product)
    {
        $this->productForm->setProduct($product);
    }

    public function editProduct(): void
    {
        $this->productForm->validate();
    }

    public function render(): View
    {
        return view('livewire.components.service.products-form');
    }
}
