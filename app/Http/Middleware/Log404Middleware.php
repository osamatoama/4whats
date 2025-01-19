<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Log404Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        if ($response->getStatusCode() === 404) {
            logger()->error('404 Not Found', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        
        return $response;
    }
} 