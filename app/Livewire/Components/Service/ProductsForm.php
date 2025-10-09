<?php

namespace App\Livewire\Components\Service;

use App\Livewire\Forms\Service\ProductForm;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductsForm extends Component
{
    public ProductForm $form;

    public $productId;

    public function boot()
    {
        $this->product($this->productId);
    }

    public function product($id): void
    {
        $this->form->setProduct(Product::findOrFail($id));
        $this->dispatch('product-edit');
        $this->pull('productId');

    }

    public function update(): void
    {
        $this->form->update();
        session()->flash('status', 'Produto Atualizado com Sucesso.');
        $this->dispatch('close-modal');

    }


    public function render(): View
    {
        return view('livewire.components.service.products-form');
    }
}
