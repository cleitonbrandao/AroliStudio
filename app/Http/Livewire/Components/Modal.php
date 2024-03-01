<?php

namespace App\Livewire\Components;

use App\Traits\WithModal;
use Livewire\Component;

class Modal extends Component
{
    use WithModal;
    public $component;
    public array $params = [];
    public $show = false;
    protected $listeners = ['open', 'openModal', 'close'];

    public function open($component = null, $params = [])
    {
        $this->show = true;
        $this->component = $component;
        $this->params = $params;
    }

    public function close()
    {
        $this->show = false;
    }
    public function render()
    {
        return view('livewire.components.modal');
    }
}
