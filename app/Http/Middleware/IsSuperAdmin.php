<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Traits\RespondsWithHttpStatus;
class IsSuperAdmin
{
    use RespondsWithHttpStatus;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->is_super_admin == 0){
            return $this->failure("you haven't permission to do this action",403);
        }
        return $next($request);
    }
}
