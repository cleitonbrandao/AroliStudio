<?php

namespace App\Livewire\Service;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPagination extends Component
{
    use WithPagination;

    public Product $selectedProduct;

    #[On('product-edit')]
    public function modalEditProduct(Product $product): void
    {
        $this->selectedProduct = $product;
        $this->dispatch('open-modal', name:  'product-edit');
    }
    public function render(): View
    {
        return view('livewire.service.products-pagination',
            [
                'products' => Product::where('team_id', Auth::user()->currentTeam->id)
                                 ->paginate(5)
            ]
        );
    }
}
