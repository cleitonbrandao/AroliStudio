<?php

namespace App\Http\Controllers;

use App\Enums\Route;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Vincula o produto ao team atual do usuário
        $validated['team_id'] = Auth::user()->currentTeam->id;

        Product::create($validated);

        return redirect()
            ->route(Route::WEB_ROOT_NEGOTIABLE)
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    /**
     * Update the specified product.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        // Verifica se o produto pertence ao team do usuário
        if ($product->team_id !== Auth::user()->currentTeam->id) {
            abort(Response::HTTP_FORBIDDEN, 'Você não tem permissão para editar este produto.');
        }

        $product->update($request->validated());

        return redirect()
            ->route(Route::WEB_ROOT_NEGOTIABLE)
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Verifica se o produto pertence ao team do usuário
        if ($product->team_id !== Auth::user()->currentTeam->id) {
            abort(Response::HTTP_FORBIDDEN, 'Você não tem permissão para excluir este produto.');
        }

        $product->delete();

        return redirect()
            ->route(Route::WEB_ROOT_NEGOTIABLE)
            ->with('success', 'Produto excluído com sucesso!');
    }
}
