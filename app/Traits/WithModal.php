<?php

namespace App\Traits;


trait WithModal
{

    public function openModal($component = null, $params = null)
    {
//        dd('dentro do openModal');
        $this->dispatch('open')->to('components.modal');
    }

    public function closeModal()
    {
//       dd($this->listeners);
        $this->dispatch('close')->to('components.modal');
    }
}
