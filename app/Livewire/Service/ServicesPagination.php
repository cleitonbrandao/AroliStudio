<?php

namespace App\Livewire\Service;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServicesPagination extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.service.services-pagination',
            [
                'services' =>  Service::paginate(5)
            ]
        );
    }
}
