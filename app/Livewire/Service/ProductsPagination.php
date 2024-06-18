<?php

namespace App\Livewire\Service;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPagination extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.service.products-pagination',
            [
                'products' => Product::paginate(5)
            ]
        );
    }
}
