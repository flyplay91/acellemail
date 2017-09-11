<?php

namespace Acelle\Http\Middleware;

use Closure;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check api auth
        if (!isset($request->api_key)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        return $next($request);
    }
}
