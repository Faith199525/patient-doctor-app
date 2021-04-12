<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next,$role)
    {
        if (! $request->user()->hasRole($role)) {
            
            return response()->json([
                'message' => 'You are not a '.ucfirst($role).'!',
                'statusCode' => 403
            ], 403);

        }
        return $next($request);
    }
}
