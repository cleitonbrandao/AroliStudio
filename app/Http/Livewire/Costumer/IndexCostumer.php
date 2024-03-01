<?php

namespace App\Livewire\Costumer;

use App\Models\Costumer;
use App\Traits\WithModal;
use Livewire\Component;

class IndexCostumer extends Component
{
    public $costumers;
    public function index()
    {
        $this->costumers = Costumer::with('people')->get();
        return view('livewire.costumer.index');
    }
}
