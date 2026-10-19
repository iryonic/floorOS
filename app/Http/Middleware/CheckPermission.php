<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized action',
                    'message' => "You do not have the required permission: {$permission}"
                ], 403);
            }

            abort(403, "Access Denied: You do not have the required permission ({$permission}) to access this resource.");
        }

        return $next($request);
    }
}
