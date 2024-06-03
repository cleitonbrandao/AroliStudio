<?php

namespace App\Livewire\Components;


use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AnySearch extends Component
{
    public array $items = [];
    public $search = '';
    public mixed $products = [];

    public bool $showDropdown = false;

    public function render(): View
    {
        $this->updatedSearch($this->search);
        return view('livewire.components.any-search', ['products' => $this->products]);
    }
    public function updatedSearch($value)
    {
        if (strlen($value) >= 3) {
            $this->showDropdown = true;
            $this->products = Product::where('name', 'like', '%' . $value . '%')->limit(7)->get();
        }
    }
    public function addItem($item):void
    {
        $this->items[] = $item;
        $this->showDropdown = false;
        Cache::put('items', $this->items);
    }
}
