<?php

namespace App\Http\Middleware;

use App\Models\Permissions\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $user_role = Role::find($user->role_id);
        $permissionName = $request->route()->getName();
        if (!$user_role->check($permissionName)) {
            return response()->json(['message' => 'Access Denied'], 401);
        }

        return $next($request);
    }
}
