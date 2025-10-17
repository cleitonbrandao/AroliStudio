<?php

namespace App\Livewire\Service;

use App\Models\Package;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class PackagesPagination extends Component
{
    public function render()
    {
        return view('livewire.service.packages-pagination',
            [
                'packages' => Package::auth()->paginate(5)
            ]
        );
    }
}
