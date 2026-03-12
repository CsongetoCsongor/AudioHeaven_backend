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

    public function destroy(Request $request)
    {
        $user = $request->user();


        if ($user->profile_picture && !str_contains($user->profile_picture, 'default')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();
        
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User account deleted successfully.'
        ], 200);
    }

    public function destroyById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => "User #{$id} has been deleted by Admin."], 200);
    }
}
