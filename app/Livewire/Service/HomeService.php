<?php

namespace App\Livewire\Service;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Service;
use Livewire\WithPagination;
class HomeService extends Component
{
    public function home()
    {
        return view('livewire.service.index');
    }
}
