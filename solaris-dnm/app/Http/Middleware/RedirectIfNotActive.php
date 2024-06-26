<?php

namespace App\Http\Middleware;

use App\User;
use Auth;
use Closure;

class RedirectIfNotActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * If user is not active or shop is waiting for payment.
         */
        if (Auth::check() && (!Auth::user()->active || Auth::user()->role === User::ROLE_SHOP_PENDING)) {
            return redirect()->guest('/auth/pending');
        }

        return $next($request);
    }
}
