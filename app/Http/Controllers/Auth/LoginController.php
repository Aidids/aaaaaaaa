<?php

namespace App\Http\Controllers\Auth;

use App\Action\LoginAction;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Laravel\Auth\ListensForLdapBindFailure;


class LoginController extends Controller
{
    use AuthenticatesUsers, ListensForLdapBindFailure;

    public function __construct()
    {
        $this->listenForLdapBindFailure();
        $this->loginAction = new LoginAction();
    }

    use ListensForLdapBindFailure {
        handleLdapBindError as baseHandleLdapBindError;
    }

    public function show()
    {
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        $this->loginAction->handle($user);

        return redirect()->route('dashboard.index');
    }

    protected function credentials(Request $request)
    {
        $credentials = [
            'samaccountname' => $request->username,
            'password' => $request->password,
        ];

        return $credentials;
    }

    public function username()
    {
        return 'username';
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out
//        auth()->user()->tokens()->delete();

        $request->session()->invalidate(); // Invalidate the session

        // Optionally, you can also regenerate the session ID
        $request->session()->regenerateToken(); // generate random string

        return redirect('/hq/login');
    }
}
