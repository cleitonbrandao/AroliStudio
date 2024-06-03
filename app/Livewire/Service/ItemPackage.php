<?php

namespace App\Livewire\Service;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemPackage extends Component
{
    #[On('items-update')]
    public function render()
    {
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
