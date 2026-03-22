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
        $playlists = $request->user()->playlists()->with('songs')->get();

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
    // App\Http\Controllers\PlaylistController.php

public function show($id)
    {
        // 1. Betöltjük a playlistet
        // 2. Betöltjük a tulajdonost (user)
        // 3. Betöltjük a dalokat (songs)
        // 4. A dalokon BELÜL betöltjük a feltöltőt (user) ÉS az albumot (album) is
        $playlist = Playlist::with([
            'user:id,name',
            'songs.user:id,name',
            'songs.album:id,title'
        ])->findOrFail($id);

        return response()->json([
            'id' => $playlist->id,
            'title' => $playlist->title,
            'user' => $playlist->user,
            'created_at' => $playlist->created_at,
            'songs' => $playlist->songs
        ], 200);
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
    public function destroy(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);

        if ($playlist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized!'], 403);
        }

        $playlist->delete();

        return response()->json(['message' => 'Playlist deleted succesfully!']);
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
