<?php

namespace App\Livewire\Service;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemPackage extends Component
{
    public array $packages_items = [];
    #[On('items-update')]
    public function render()
    {
        $products = Cache::get('packages_items.products', []);
        $services = Cache::get('packages_items.services', []);
        $this->packages_items = [$products, $services];
//        dd($packages_items);
        return view('livewire.service.item-package');
    }
    public function removeItem($item)
    {
        $items = cache('items');
        if (isset($items[$item]))
        {
            unset($items[$item]);
            $items = array_values($items);
            Cache::put('items', $items);
        }
    }
}
