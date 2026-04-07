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
    // 1. LÉPÉS: E-mail küldése a linkkel
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // A Password::sendResetLink elküldi a levelet a gyári sablonnal
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => __($status)], 400);
    }

    // 2. LÉPÉS: A tényleges jelszó megváltoztatása
    public function reset(Request $request)
{
    // 1. Kikeressük az emailt a token alapján manuálisan
    // Mivel a Laravel hasheli a tokeneket, a Password Broker segítségével érdemes ellenőrizni,
    // de ha egyszerűen akarod:
    $record = DB::table('password_reset_tokens')->get()->filter(function($item) use ($request) {
        return Hash::check($request->token, $item->token);
    })->first();

    // Ha nincs meg a rekord, akkor tényleg rossz a token
    if (!$record) {
        return response()->json(['message' => 'Invalid or expired token'], 400);
    }

    // 2. BELERAKJUK az emailt a requestbe, mintha a frontend küldte volna
    $request->merge(['email' => $record->email]);

    // 3. Most már a gyári reset() tudni fogja az emailt, és nem fog "Invalid token"-t dobni
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
