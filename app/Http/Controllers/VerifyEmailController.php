<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function resendNotification(Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    }

    public function verify(Request $request, $id, $hash) {
        try {
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
        catch (\Throwable $th) {
            return redirect('http://localhost:8080/home?verification=error');
        }
    }

}
