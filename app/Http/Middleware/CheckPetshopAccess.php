<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPetshopAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if petshop menu is enabled
        $isPetshopEnabled = Setting::get('show_petshop_menu', '1') === '1';
        
        // If not enabled, deny access with custom error page
        if (!$isPetshopEnabled) {
            abort(403, 'Mohon maaf, layanan Petshop saat ini sedang tidak tersedia. Silakan hubungi kami untuk informasi lebih lanjut atau coba lagi nanti.');
        }
        
        return $next($request);
    }
}
