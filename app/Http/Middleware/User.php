<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check()) {
            return redirect()->route('user_login');
        }
        // Check if user is verified
        if (Auth::guard('web')->user()->status == 0) {
            Auth::guard('web')->logout();
            return redirect()->route('user_login')
                ->with('error', 'Your email is not verified. Please verify your email first.');
        }
        return $next($request);
    }
}
