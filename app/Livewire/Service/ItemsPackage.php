<?php

namespace App\Livewire\Service;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemsPackage extends Component
{
    public function render()
    {
        return view('livewire.service.items-package');
    }
}
