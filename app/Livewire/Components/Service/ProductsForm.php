<?php

namespace App\Livewire\Components\Service;

use App\Livewire\Forms\Service\ProductForm;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ProductsForm extends Component
{
    public ProductForm $productForm;
    #[Reactive]
    public Product $selectedProduct;

    public function boot(): void
    {
        $this->loadProduct();
    }

    public function loadProduct(): void
    {
        $this->productForm->setProduct($this->selectedProduct);

    }
    public function editProduct(): void
    {
        $this->productForm->update();
        session()->flash('status', 'Produto atualizado com Sucesso.');
        $this->dispatch('product-edit');
    }

    public function render(): View
    {
        return view('livewire.components.service.products-form', [
            'productForm' => $this->productForm,
        ]);
    }
}
