<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json([
        //     'detected_mime' => $request->file('audio')->getMimeType(),
        //     'client_mime' => $request->file('audio')->getClientMimeType(),
        //     'extension' => $request->file('audio')->getClientOriginalExtension()
        // ]);

        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'audio' => 'required|file|mimetypes:audio/mpeg,text/plain,application/octet-stream|extensions:mp3,wav,ogg|max:20000',
            'cover' => 'required|image|mimes:jpg,jpeg,png|max:5000',
            'album_id' => 'nullable|exists:albums,id'
        ]);

        $audioPath = $request->file('audio')->store('songs', 'public');
        $coverPath = $request->file('cover')->store('covers', 'public');

        $song = Song::create([
            'title' => $fields['title'],
            'plays' => 0,
            'stored_at' => asset('storage/' . $audioPath),
            'cover' => asset('storage/' . $coverPath),
            'user_id' => $request->user()->id,
            'album_id' => $fields['album_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Song uploaded succesfully!',
            'song' => $song
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Song $song)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Song $song)
    {
        //
    }

    public function listByUser($id)
    {
        // Megkeressük a felhasználót
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        // Lekérjük a felhasználóhoz tartozó összes zenét
        // A 'songs' kapcsolatot már definiáltad a User modellben
        $songs = $user->songs;

        return response()->json($songs, 200);
    }
}
