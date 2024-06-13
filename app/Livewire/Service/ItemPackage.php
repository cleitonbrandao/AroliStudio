<?php

namespace App\Livewire\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemPackage extends Component
{
    public mixed $packages_items = [];
    public float $price_cost = 0;
    #[On('items-update')]
    public function render()
    {
        $this->packages_items = Cache::get('packages_items', []);
        foreach ($this->packages_items as $item) {
            dump($item->cost_price);
            $this->price_cost += $item->cost_price;
            dump($this->price_cost);
        }
        return view('livewire.service.item-package');
    }
    public function removeItem($item)
    {
        $items = cache('packages_items');
        if (isset($items[$item]))
        {
            Arr::forget($items, $item);
            Cache::put('packages_items', array_values($items));
            $this->dispatch('remove-items-update');
        }
    }
}
