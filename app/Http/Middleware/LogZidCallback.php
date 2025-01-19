<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogZidCallback
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Zid OAuth Callback Request', [
            'full_url' => $request->fullUrl(),
            'path' => $request->path(),
            'query_params' => $request->query(),
            'headers' => $request->headers->all(),
        ]);

        return $next($request);
    }
} 