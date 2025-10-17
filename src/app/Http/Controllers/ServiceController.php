<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['team_id'] = Auth::user()->currentTeam->id;
        
        Service::create($data);
        return redirect('negotiable');
    }
}
