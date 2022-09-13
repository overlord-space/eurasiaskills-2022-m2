<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CustomTokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization')) {
            $tokenString = substr($request->header('Authorization'), 7);

            if (!empty($tokenString)) {
                $token = PersonalAccessToken::query()->where('token', '=', $tokenString)->first();

                if ($token) {
                    Auth::loginUsingId($token->tokenable_id);
                }
            }
        }

        return $next($request);
    }
}
