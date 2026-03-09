<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\ListeningHistoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $songs = Song::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->with(['user:id,name', 'album:id,title']) // Kapcsolt adatok betöltése (opcionális, de hasznos)
            ->get();

        return response()->json($songs, 200);
    }

    public function random(Request $request)
    {
        
        $count = $request->query('count', 10);

        $songs = Song::inRandomOrder()
            ->limit($count)
            ->with(['user:id,name', 'album:id,title'])
            ->get();

        return response()->json($songs);
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
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:20000',
            'cover' => 'required|image|mimes:jpg,jpeg,png|max:5000',
            'album_id' => 'nullable|exists:albums,id'
        ]);

        $audioPath = $request->file('audio')->store('songs', 'public');
        $coverPath = $request->file('cover')->store('covers', 'public');

        $song = Song::create([
            'title' => $fields['title'],
            'plays' => 0,
            'stored_at' => 'app/public/' . $audioPath,
            'cover' => 'storage/' . $coverPath,
            'user_id' => $request->user()->id,
            'album_id' => null
        ]);

        return response()->json([
            'message' => 'Song uploaded succesfully!',
            'song' => $song
        ], 201);
    }

    public function play($id)
    {
        $song = Song::find($id);

        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        $path = storage_path($song->stored_at);

        if (!is_file($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // if (!is_file($path)) {
        //     return $this->play(1);
        // }
        $size = filesize($path);
        $start = 0;
        $end = $size - 1;

        

        $headers = [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes',
        ];

        if (request()->hasHeader('Range')) {

            preg_match('/bytes=(\d+)-(\d*)/', request()->header('Range'), $matches);

            $start = intval($matches[1]);
            $end = $matches[2] !== '' ? intval($matches[2]) : $end;

            if ($end >= $size) {
                $end = $size - 1;
            }

            $length = $end - $start + 1;

            return response()->stream(function () use ($path, $start, $length) {

                $file = fopen($path, 'rb');
                fseek($file, $start);

                $buffer = 8192;
                $bytesToRead = $length;

                while ($bytesToRead > 0 && !feof($file)) {
                    $read = ($bytesToRead > $buffer) ? $buffer : $bytesToRead;
                    echo fread($file, $read);
                    flush();
                    $bytesToRead -= $read;
                }

                fclose($file);

            }, 206, array_merge($headers, [
                'Content-Range' => "bytes $start-$end/$size",
                'Content-Length' => $length,
            ]));
        }

        return response()->stream(function () use ($path) {
            readfile($path);
        }, 200, array_merge($headers, [
            'Content-Length' => $size,
        ]));
    }

    public function logPlay(Request $request, $id)
    {
    //     if (!$request->user()) {
    //     return response()->json([
    //         'debug_error' => 'A Laravel szerint nincs bejelentkezve senki',
    //         'token_present' => $request->bearerToken() ? 'Igen' : 'Nem',
    //         'headers' => $request->headers->all() 
    //     ], 401);
    // }

        $song = Song::find($id);

        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        try {
            DB::transaction(function () use ($request, $song) {
                // 1. Plays növelése
                $song->increment('plays');

                // 2. History mentése
                // Megpróbáljuk lekérni a bejelentkezett felhasználót (ha van token)
                $user = auth('sanctum')->user();
                
                ListeningHistoryItem::create([
                    'user_id'     => $user ? $user->id : null, // Ha nincs bejelentkezve, marad a 3-as teszt user, vagy null
                    'song_id'     => $song->id,
                    'album_id'    => $song->album_id,
                    'playlist_id' => $request->input('playlist_id'), // POST body-ból vagy query-ből
                ]);
            });

            return response()->json(['message' => 'Play logged successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to log play',
                'message' => $e->getMessage()
            ], 500);
        }
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
