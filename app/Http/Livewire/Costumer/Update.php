<?php

namespace App\Livewire\Costumer;

use App\Traits\WithModal;
use Livewire\Component;

class Update extends Component
{
    public $title;
    public $name;
    public function mount()
    {
        dd('mount');
//        $this->title = $title;
//        $this->name = $name;
    }
    public function render()
    {
        return view('livewire.costumer.update');
    }
}
