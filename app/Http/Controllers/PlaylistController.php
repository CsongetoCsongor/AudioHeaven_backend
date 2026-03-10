<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // App\Http\Controllers\PlaylistController.php

    public function addSong(Request $request, $playlistId)
    {
        $request->validate([
            'song_id' => 'required|exists:songs,id',
        ]);

        $playlist = Playlist::findOrFail($playlistId);

        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Ezt a listát nem te hoztad létre!'], 403);
        }

        $playlist->songs()->syncWithoutDetaching([$request->song_id]);

        return response()->json([
            'message' => 'Dal sikeresen hozzáadva a lejátszási listához!',
            'playlist' => $playlist->load('songs')
        ]);
    }

    public function index(Request $request)
    {
        $playlists = $request->user()->playlists()->get();

        return response()->json($playlists, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // 2. Létrehozás (a bejelentkezett felhasználóhoz kötve)
        $playlist = Playlist::create([
            'title' => $fields['title'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Playlist created successfully!',
            'playlist' => $playlist
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playlist $playlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        //
    }
}
