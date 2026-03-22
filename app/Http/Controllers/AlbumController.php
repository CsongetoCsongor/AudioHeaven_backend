<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use App\Models\User;
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

    public function listByUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $albums = Album::with('user:id,name', 'songs')->where('user_id', $id)->get();
        // $albums = $user->albums;

        return response()->json($albums, 200);
    }

    public function random(Request $request)
    {
        $count = $request->query('count', 10);

        $albums = Album::inRandomOrder()
            ->limit($count)
            ->with('user:id,name')
            ->get();

        return response()->json($albums);
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
                $albumCoverPath = $request->file('album_cover')->store('covers', 'public');

                $album = Album::create([
                    'title' => $request->title,
                    'album_cover' => 'storage/' . $albumCoverPath,
                    'user_id' => $request->user()->id,
                ]);

                foreach ($request->file('songs') as $index => $songData) {
                    $audioFile = $songData['audio'];
                    $songTitle = $request->input("songs.$index.title");
                    $audioPath = $audioFile->store('songs', 'public');

                    $getID3 = new \getID3;
                    $fileInfo = $getID3->analyze($audioFile->getRealPath());

                    $duration = isset($fileInfo['playtime_seconds'])
                            ? (int)round($fileInfo['playtime_seconds'])
                            : 0;

                    $album->songs()->create([
                        'title' => $songTitle,
                        'plays' => 0,
                        'length' => $duration,
                        'stored_at' => 'app/public/' . $audioPath,
                        'cover' => 'storage/' . $albumCoverPath,
                        'user_id' => $request->user()->id,
                    ]);
                }

                return response()->json($album->load('songs'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error happaned during the uploading!', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    public function show($id)
    {

        $album = Album::findOrFail($id);

        return response()->json([
            'id' => $album->id,
            'title' => $album->title,
            'album_cover' => $album->album_cover,
            'user_id' => $album->user_id,
            'user' => $album->user()->select('id', 'name')->first(),
            'created_at' => $album->created_at,
            'songs' => $album->songs()->with('user:id,name')->get(),
        ], 200);
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
