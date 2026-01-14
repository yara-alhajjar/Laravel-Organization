<?php

namespace App\Http\Middleware;
use App\Models\Manager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        
        if ($request->user() instanceof Manager) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized. Manager access required.'], 403);
    }
}
