<?php

namespace App\Livewire\Service;

use Illuminate\View\View;
use Livewire\Component;

class RegisterProduct extends Component
{
    public function render(): View
    {
        return view('livewire.service.form-product');
    }
}
