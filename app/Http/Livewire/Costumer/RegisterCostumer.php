<?php

namespace App\Livewire\Costumer;

use Livewire\Component;

class RegisterCostumer extends Component
{
    public function render()
    {
        return view('livewire.costumer.form');
    }
    public function update()
    {
        return view('livewire.costumer.update');
    }
}