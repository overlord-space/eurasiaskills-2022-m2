<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::make($request->validated());
        $user->save();

        return response()->redirectTo('/login')->with('status', 'User successfully registered!');
    }

    public function login(LoginRequest $request)
    {
        $request->attempt();

        return response()->redirectTo('/projects')->with('status', 'You are successfully logged in!');
    }

    public function logout()
    {
        Auth::logout();

        return response()->redirectTo('/login')->with('status', "Successful logout");
    }
}
