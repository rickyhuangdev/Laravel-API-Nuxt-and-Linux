<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\IUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IUser $user)
    {
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->user = $user;
    }

    public function verify(Request $request)
    {
        if (!URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
                "message" => "Invalid verification link"
            ]], 422);
        }
        $user = User::findOrFail($request->user);
        if (!$user) {
            return response()->json(["errors" => [
                "message" => "No User could be found with this email address"
            ]], 422);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }
        $user->markEmailAsVerified();
        event(new Verified($user));
        return response()->json([
            "message" => "Email successfully verified"
        ], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);
        $user = $this->user->findWhereFirst('email', $request->email);
        if (!$user) {
            return response()->json(["errors" => [
                "email" => "No User could be found with this email address"
            ]], 422);
        }
        $user->sendEmailVerificationNotification();
        return response()->json([
            "status" => "verification link resent"
        ]);
    }
}
