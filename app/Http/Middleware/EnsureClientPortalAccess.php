<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientPortalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('client_portal_email')) {
            return redirect()
                ->route('client-area.login')
                ->with('status_error', 'Richiedi un link di accesso per entrare nella tua area privata.');
        }

        return $next($request);
    }
}
