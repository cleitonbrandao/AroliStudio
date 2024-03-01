<?php

namespace App\Http\Controllers;

use App\Models\Costumer;
use App\Http\Requests\StoreCostumerRequest;
use App\Models\People;
use Illuminate\Http\RedirectResponse;

class CostumerController extends Controller
{
    public function store(StoreCostumerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $people = People::create($data['person']);
        $people->costumer()->create($data['costumer']);
        return redirect('costumer/index');
    }
}
