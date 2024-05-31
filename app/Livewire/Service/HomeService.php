<?php

namespace App\Livewire\Service;

use Livewire\Component;
//use App\Models\Service;
class HomeService extends Component
{
    public function home()
    {
        return view('livewire.service.index',
//            ['services' => Services::all()]
        );
    }
}
