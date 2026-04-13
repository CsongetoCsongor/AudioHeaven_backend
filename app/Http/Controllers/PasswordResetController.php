<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => __($status)], 400);
    }


    public function reset(Request $request)
{

    $record = DB::table('password_reset_tokens')->get()->filter(function($item) use ($request) {
        return Hash::check($request->token, $item->token);
    })->first();


    if (!$record) {
        return response()->json(['message' => 'Invalid or expired token'], 400);
    }


    $request->merge(['email' => $record->email]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Password has been reset.'])
        : response()->json(['message' => __($status)], 400);
}
}
