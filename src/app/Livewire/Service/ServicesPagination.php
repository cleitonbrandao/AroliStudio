<?php

namespace App\Livewire\Service;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
class ServicesPagination extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.service.services-pagination',
            [
                'services' =>  Service::auth()->paginate(5)
            ]
        );
    }
}
