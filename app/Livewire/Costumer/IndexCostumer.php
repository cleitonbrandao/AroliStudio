<?php

namespace App\Livewire\Costumer;

use App\Models\Costumer;
use App\Traits\WithModal;
use Livewire\Component;

class IndexCostumer extends Component
{
    use WithModal;

    public function index()
    {
        return view('livewire.costumer.index')->with(['costumers' => Costumer::with('people')->get()]);
    }
}
