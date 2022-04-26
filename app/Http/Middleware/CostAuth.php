<?php

namespace App\Http\Middleware;

use Closure;

class CostAuth
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
        //セッションの値を確認する
		if($request->session()->get('cost_auth') == false){
            
			return redirect("/");
		}
		
		return $next($request);
    }
}
