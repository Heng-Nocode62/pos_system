<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,string $role): Response
    {
        $user = $request->user();
        if(!$user){
            return response()->json(
                ['message'=>'Unauthenticated'],401
            );
        }

        if(!$user->role){
            return response()->json([
                'message'=>'No role assigned.'
            ],403);
        }

        $allowedRoles = explode('|',$role);
        if(!$user->hasRole($allowedRoles)){
            return response()->json([
                'message'=>"Forbidden."
            ],403);
        }
        return $next($request);
    }
}
