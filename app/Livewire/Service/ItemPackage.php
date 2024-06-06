<?php

namespace App\Livewire\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemPackage extends Component
{
    public mixed $packages_items = [];
    #[On('items-update')]
    public function render()
    {
        $this->packages_items = Cache::get('packages_items', []);
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
