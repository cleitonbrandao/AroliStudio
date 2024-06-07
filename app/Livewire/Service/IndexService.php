<?php

namespace App\Livewire\Service;

use Livewire\Component;

class IndexService extends Component
{
    public function index()
    {
        dump("aqui");
        return view('livewire.service.index');
    }
}
