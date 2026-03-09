<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            // Csak a publikus adatokat adjuk vissza, az emailt/időpontokat nem feltétlen kell
            ->select('id', 'name', 'profile_picture') 
            ->get();

        return response()->json($users, 200);
    }

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

    public function me(Request $request) {
        return $request->user();
    }
}
