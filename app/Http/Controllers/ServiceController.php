<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceController;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function store(StoreServiceController $request): RedirectResponse
    {
        Service::create($request->validated());
        return redirect('negotiable');
    }
}
