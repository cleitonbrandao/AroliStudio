<?php

namespace App\Livewire\Employee;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.employee.home')]
class RegisterEmployee extends Component
{
    public function render()
    {
        return view('livewire.employee.form');
    }
}
