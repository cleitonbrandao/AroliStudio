<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());
        return redirect('negotiable');
    }
}
