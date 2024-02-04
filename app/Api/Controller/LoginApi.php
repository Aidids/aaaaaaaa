<?php

namespace App\Api\Controller;

use App\Action\LoginAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginApi extends Controller
{
    public function login(Request $request, LoginAction $loginAction)
    {
        $credentials = [
            'samaccountname' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials))
        {
            $loginAction->handle(Auth::user());

            return response()->json([
                'token' => Auth::user()->getRememberToken()
            ]);
        }

        return response()->json('Salah brooo ', 404);
    }
}
