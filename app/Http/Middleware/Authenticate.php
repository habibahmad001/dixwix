<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     return $request->expectsJson() ? null : route('login');
    // }
    public function authenticate($request,array $guards)
    {
        $route = $request->route();

		if(is_array($route)){
			list($controller, $method) = explode('@', $route[1]['uses']);
		}
		else{
			list($controller, $method) = explode('@', $route->action['uses']);
		}

        $body = $request->getContent();

        try {
            $user = Auth::user();
            $data['user_id'] = (isset($user)) ? $user->user_id : null;
            $data['body'] = $body;
            $data['fname'] = $method;
            $data['controller'] = $controller;
        }
        catch (Exception $e) {
            $this->redirectTo($request);
        }
    }

    public function handle($request, Closure $next, ...$guards){
        if (Auth::check()) {
            return $next($request);
        }
        session()->put('redirect_url', $request->fullUrl());
        return redirect()->route('login');
    }
}
