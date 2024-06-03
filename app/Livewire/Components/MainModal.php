<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Features\SupportAttributes\AttributeCollection;

class MainModal extends Component
{
    public AttributeCollection $attributes;
    public function render()
    {
        return view('livewire.components.main-modal');
    }
}
