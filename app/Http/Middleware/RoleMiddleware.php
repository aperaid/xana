<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class RoleMiddleware
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
    if ( Auth::check() && Auth::user()->access() == 'Admin'){
      return $next($request);
    }elseif ( Auth::check() && Auth::user()->access() == 'POPPN'){
      return $next($request);
    }else {
      return redirect()->back();
    } 
  }
}
