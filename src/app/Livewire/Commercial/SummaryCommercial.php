<?php

namespace App\Livewire\Commercial;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.commercial.home')]
class SummaryCommercial extends Component
{
    public function render()
    {
        return view('livewire.commercial.summary-commercial');
    }
}
