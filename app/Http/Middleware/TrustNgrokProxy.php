<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustNgrokProxy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Trust ngrok proxy headers
        if ($request->hasHeader('X-Original-Host')) {
            $request->headers->set('X-Forwarded-Host', $request->header('X-Original-Host'));
        }

        // Set proper scheme for ngrok HTTPS
        if ($request->header('X-Forwarded-Proto') === 'https' || str_contains($request->header('Host', ''), 'ngrok')) {
            $request->server->set('HTTPS', 'on');
            $_SERVER['HTTPS'] = 'on';
        }

        return $next($request);
    }
}
