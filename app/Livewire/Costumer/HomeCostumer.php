<?php

namespace App\Livewire\Costumer;
use LivewireUI\Modal\ModalComponent;


class HomeCostumer extends ModalComponent
{
    public function home()
    {
        return view('layouts.costumer.home');
    }
}
