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
            ->select('id', 'name', 'profile_picture') 
            ->get();

        return response()->json($users, 200);
    }

    public function random(Request $request)
    {
        $count = $request->query('count', 10);

        $users = User::inRandomOrder()
            ->limit($count)
            ->select('id', 'name', 'profile_picture')
            ->get();

        return response()->json($users);
    }

    public function show($id) {
    $user = User::findOrFail($id);

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'profile_picture' => $user->profile_picture,
        'email' => $user->email,
        'created_at' => $user->created_at
    ], 200);
    }

    public function me(Request $request) {
        return $request->user();
    }
}
