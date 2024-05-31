<?php

namespace App\Livewire\Service;

use Livewire\Component;

class RegisterProduct extends Component
{
    public function form()
    {
        return view('livewire.service.form-product');
    }
}
