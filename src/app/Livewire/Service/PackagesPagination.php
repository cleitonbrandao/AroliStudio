<?php

namespace App\Livewire\Service;

use App\Models\Package;
use Livewire\Component;

class PackagesPagination extends Component
{
    public function render()
    {
        return view('livewire.service.packages-pagination',
            [
                'packages' => Package::paginate(5)
            ]
        );
    }
}
