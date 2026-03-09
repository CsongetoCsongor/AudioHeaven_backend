<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $albums = Album::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->with(['user:id,name'])
            ->get();

        return response()->json($albums, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validáció
        $request->validate([
            'title' => 'required|string|max:255',
            'album_cover' => 'required|image|mimes:jpg,jpeg,png|max:5000',
            'songs' => 'required|array|min:1',
            'songs.*.title' => 'required|string|max:255',
            'songs.*.audio' => 'required|file|mimes:mp3,wav,ogg|max:20000',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Album borítókép mentése
                $albumCoverPath = $request->file('album_cover')->store('covers', 'public');

                // 3. Album létrehozása
                $album = Album::create([
                    'title' => $request->title,
                    'album_cover' => 'app/public/' . $albumCoverPath,
                    'user_id' => $request->user()->id,
                ]);

                // 4. Zenék feldolgozása
                foreach ($request->file('songs') as $index => $songData) {
                    // A validáció miatt bízhatunk benne, hogy az indexek egyeznek a songs.*.title-lel
                    $songTitle = $request->input("songs.$index.title");
                    $audioPath = $songData['audio']->store('songs', 'public');

                    $album->songs()->create([
                        'title' => $songTitle,
                        'plays' => 0,
                        'stored_at' => 'app/public/' . $audioPath,
                        'cover' => 'storage/' . $albumCoverPath, // Az album borítóját kapja a zene is
                        'user_id' => $request->user()->id,
                    ]);
                }

                // Betöltjük a zenéket a válaszhoz
                return response()->json($album->load('songs'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hiba történt a feltöltés során!', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        //
    }
}
