<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use function Symfony\Component\String\u;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function attemptLogin(Request $request): bool
    {
        //attempt to issue a token to the user based on the login credentials
        $token = $this->guard()->attempt($this->credentials($request));
        if (!$token) {
            return false;
        }
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return false;
        }
        // set user token
        $this->guard()->setToken($token);
        return true;
    }

    protected function sendLoginResponse(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->clearLoginAttempts($request);
        // get token from the authentication guard
        $token = (string)$this->guard()->getToken();
        //extract the expiry date of the token
        $expiration = $this->guard()->getPayload()->get('exp');
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return response()->json([
                "errors" => [
                    "message" => "You need to verify your email account"
                ]
            ], 422);
        }
        throw ValidationException::withMessages([
            $this->username() => 'Wrong username or password',
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
