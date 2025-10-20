<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Adiciona team_id do usuário atual
        $data['package']['team_id'] = Auth::user()->currentTeam->id;
        
        $package = Package::create($data['package']);
        if(Arr::exists($data['items'], 'products')){
            $package->products()->attach($data['items']['products']);
        }
        if(Arr::exists($data['items'], 'services')) {
            $package->services()->attach($data['items']['services']);
        }
        if(Arr::exists($data['items'], 'packages')) {
            $package->groups()->attach($data['items']['packages']);
        }
        Cache::forget('packages_items');
        return redirect('negotiable');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        //
    }
}
