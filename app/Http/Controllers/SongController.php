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
            'cover' => 'app/public/' . $coverPath,
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

        if (!is_file($path)) {
            return $this->play(1);
        }

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
