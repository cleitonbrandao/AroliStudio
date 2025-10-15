<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Se usuário não tem empresa, redirecionar para criação
        if ($user->ownedTeams()->count() === 0) {
            return redirect()->route('companies.create')
                ->with('info', 'Primeiro, crie uma empresa para começar a usar o sistema.');
        }

        return $next($request);
    }
}
