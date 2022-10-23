<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
		$permissions = [
            'user' => 'isNotAdmin',
			'admin' => 'isAdmin',
			'advisor' => 'isAdvisor',
			'manager' => 'isManager'
		];

		if (Auth::user()->{$permissions[$role]}()) {
			return $next($request);
		}

		return redirect(route('dashboard.index'));
    }
}
