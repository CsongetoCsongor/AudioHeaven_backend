<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'username' => 'required|string|unique:users,name',
            'email' => 'required|string|unique:users,email',
            'password' => ['required', 'string', Password::defaults()],
            // Kicseréltük: nullable, így nem hiba, ha üres
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:10000'
        ]);

        // Alapértelmezett érték beállítása
        $path = 'app/public/defaults/default_profile_picture.png';

        // Ha érkezett fájl, felülírjuk az alapértelmezettet
        if ($request->hasFile('profile_picture')) {
            $path = 'app/public/' . $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user = User::create([
            'name' => $fields['username'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            // Egységesítjük az elérési utat
            'profile_picture' => $path 
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Hibás adatok'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out succesfully!'],200);
    }
}
