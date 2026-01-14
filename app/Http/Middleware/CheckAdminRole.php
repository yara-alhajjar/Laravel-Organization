<?php

namespace App\Http\Middleware;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
        {
            
            if ($request->user() instanceof Admin) {
                return $next($request);
            }

            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }
}
