<?php

namespace App\Livewire\Employee;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.employee.home')]
class IndexEmployee extends Component
{

    public function index()
    {
        return view('livewire.employee.index');
    }


}
