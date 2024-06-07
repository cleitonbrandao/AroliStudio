<?php

namespace App\Livewire\Service;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPagination extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.service.products-pagination',
            [
                'products' => Product::paginate(5)
            ]
        );
    }
}
