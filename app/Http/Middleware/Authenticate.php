<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next363
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            return self::return_method($request);
        }
        return $next($request);
    }

    private function return_method($request)
    {
        if ($request->isMethod('post')) {
            return response(['message' => 'Вы не авторизованы!', 'success' => false]);
        } else {
            return abort(404);
        }
    }
}