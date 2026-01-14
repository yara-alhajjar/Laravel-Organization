<?php

namespace App\Http\Middleware;
use App\Models\Admin;
use App\Models\Manager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrManagerRole
{

    public function handle(Request $request, Closure $next): Response
        {
            $user = $request->user();

            if ($user instanceof Admin || $user instanceof Manager) {
                return $next($request);
            }

            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
}
