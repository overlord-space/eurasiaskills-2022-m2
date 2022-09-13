<?php

namespace App\Http\Controllers;

use App\Http\Requests\TokenCreateRequest;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function indexToken(Request $request)
    {
        $tokens = $request->user()->tokens()->get();

        return response()->view('pages.user.api_tokens', [
            'tokens' => $tokens,
        ]);
    }

    public function storeToken(TokenCreateRequest $request)
    {
        $request->user()->createToken($request->get('name'));

        return redirect()->back()->with('status', 'Token successfully created');
    }

    public function destroyToken(PersonalAccessToken $token)
    {
        $token->delete();

        return redirect()->back()->with('status', 'Token successfully deletd');
    }
}
