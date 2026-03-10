<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // App\Http\Controllers\PlaylistController.php

    public function addSong(Request $request, $playlistId, $songId)
    {
        $songExists = Song::where('id', $songId)->exists();
        if (!$songExists) {
            return response()->json(['message' => 'Song does not exists!'], 404);
        }

        $playlist = Playlist::findOrFail($playlistId);

        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not your playlist!'], 403);
        }

        $alreadyExists = $playlist->songs()->where('song_id', $songId)->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Song already added to playlist!'
            ], 409);
        }

        $playlist->songs()->syncWithoutDetaching([$songId]);

        return response()->json([
            'message' => 'Song succesfully added to playlist!',
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

    public function removeSong(Request $request, $playlistId, $songId)
    {
        $playlist = Playlist::findOrFail($playlistId);

        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not your playlist!'], 403);
        }
        
        if (!$playlist->songs()->where('song_id', $songId)->exists()) {
            return response()->json([
                'message' => 'Song not in playlist!'
            ], 404);
        }

        $playlist->songs()->detach($songId);

        return response()->json([
            'message' => 'Song succesfully deleted from playlist!',
            'playlist' => $playlist->load('songs')
        ], 200);
    }
}
