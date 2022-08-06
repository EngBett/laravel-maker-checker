<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        if(!auth()->check() || !auth()->user()->is_admin)  return response()->json(['message'=>'Unauthorized.'], 401);

        return $next($request);
    }

}
