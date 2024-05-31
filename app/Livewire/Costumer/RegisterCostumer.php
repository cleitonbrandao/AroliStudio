<?php

namespace App\Livewire\Costumer;
use LivewireUI\Modal\ModalComponent;

class RegisterCostumer extends ModalComponent
{
    public function render()
    {
        return view('livewire.costumer.register-costumer');
    }
    public function update()
    {
        return view('livewire.costumer.update');
    }
}
