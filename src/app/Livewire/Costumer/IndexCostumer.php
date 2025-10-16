<?php

namespace App\Livewire\Costumer;

use App\Models\Costumer;
use LivewireUI\Modal\ModalComponent;

class IndexCostumer extends ModalComponent
{

    public function index()
    {
        return view('livewire.costumer.index')->with(['costumers' => Costumer::with('people')->get()]);
    }
}
