<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use View;

class PartnersAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {

            if(Auth::user()->Roles == 3){
                return $next($request);
            }
            else{
                return redirect('/admin/login');
            }
        }
        else{
            return redirect('/admin/login');
        }
    }
}
