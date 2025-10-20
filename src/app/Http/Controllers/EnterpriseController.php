<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreEnterpriseRequest;

class EnterpriseController extends Controller
{
    public function store(StoreEnterpriseRequest $request): RedirectResponse
    {
        dd($request->all());
        $data = $request->validated();
        $enterprise = Enterprise::create($data['enterprise']);
        return redirect('enterprise/index');
    }
}
