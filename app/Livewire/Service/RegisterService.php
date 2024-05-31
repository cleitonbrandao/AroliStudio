<?php

namespace App\Livewire\Service;

use Livewire\Component;

class RegisterService extends Component
{
    public function form()
    {
        return view('livewire.service.form-service');
    }
}
