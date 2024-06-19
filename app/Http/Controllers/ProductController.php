<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());
        return redirect('negotiable');
    }
    public function update(StoreProductRequest $request): RedirectResponse
    {
        Product::updated($request->validated());
        return redirect('negotiable');
    }
}
