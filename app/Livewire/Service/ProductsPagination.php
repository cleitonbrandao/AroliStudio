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
//        $this->services = Service::where('name', 'like', '%' . $this->search . '%')->limit(3)->get();
//        $this->packages = Package::where('name', 'like', '%' . $this->search . '%')->limit(3)->get();
//        $this->packages_items = Arr::collapse([$this->products, $this->services, $this->packages]);
        return view('livewire.service.products-pagination', ['products' => Product::paginate(2)]);
    }
}
