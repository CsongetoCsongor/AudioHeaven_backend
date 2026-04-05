<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

class VerifyEmailController extends Controller
{
    // 1. Az értesítés újraküldése (ha nem kapta meg)
    public function resendNotification(Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    }

    // 2. Maga az ellenőrzés folyamata
    public function verify(Request $request, $id, $hash) {
            $user = User::findOrFail($id);

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return response()->json(['message' => 'Invalid hash'], 403);
            }

            if ($user->hasVerifiedEmail()) {
                return redirect('http://localhost:8080/login?already_verified=1');
            }

            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
            // return response()->json(['message' => 'Email verified successfully!']);
            return redirect('http://localhost:8080/home?verification=success');
    }
}
