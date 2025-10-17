<?php

namespace App\Livewire\Service;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.service.home')]
class RegisterPackage extends Component
{
    public function render()
    {
        return view('livewire.service.register-package');
    }
}
