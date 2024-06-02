<?php

namespace App\Livewire\Service;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ItemsPackage extends Component
{
//    public array $items = [];

    #[Computed]
    public function getItemsProperties()
    {
        return Cache::get('items', []);
    }

    public function render()
    {
        return view('livewire.service.items-package');
    }
}
