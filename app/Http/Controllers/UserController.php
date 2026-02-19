<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function show($id) {
    // Megkeressük a felhasználót, ha nincs, 404-et dob
    $user = User::findOrFail($id);

    return response()->json([
        'id' => $user->id,
        'name' => $user->name, // A DB-ben 'name', de az API 'username'-et vár
        'profile_picture' => $user->profile_picture,
        'email' => $user->email,
        'created_at' => $user->created_at
    ], 200);
}
}
