<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(Auth::id());
        if ($user->hasActiveSubscription()) {
            // Return an error response for API
            return (new BaseController)->sendError([
                'message' => 'Subscription is required to perform this action'
            ]);
        }

        return $next($request);
    }
}
