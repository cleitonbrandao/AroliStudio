<?php

namespace App\Livewire\Service;

use Livewire\Component;

class HomeService extends Component
{
    public function home()
    {
        return view('layouts.service.home');
    }
}
