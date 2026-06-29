<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si l'utilisateur est DNA, il a accès à tout
        if ($user && $user->isDNA()) {
            return $next($request);
        }

        $route_name = $request->route()->getName();

        if (!$user || !in_array($route_name, $user->accepted_routes)) {
            return back()->with('message.error', 'Vous n\'avez pas les droits nécessaires pour cette action');
        }

        return $next($request);
    }
}
