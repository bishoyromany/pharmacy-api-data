<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PDF2HTMLMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->password === env("HTML_TO_PDF_API_PASSWORD")) {
            return $next($request);
        }
        abort(500);
    }
}
