<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\ListeningHistoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->with(['user:id,name', 'album:id,title'])
            ->get();

        return response()->json($songs, 200);
    }

    public function listByUser($id)
    {
        $user = User::find($id);



        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        // $songs = $user->songs;
        $songs = Song::with('user:id,name')->where('user_id', $id)->get();

        return response()->json($songs, 200);
    }

    public function getNewSongs(Request $request) {
        $count = $request->query('count', 10);

        $songs = Song::with('user:id,name')->orderBy('created_at', 'desc')->limit($count)->get();

        return response()->json($songs);
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

        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:20000',
            'cover' => 'required|image|mimes:jpg,jpeg,png|max:10000',
            'album_id' => 'nullable|exists:albums,id'
        ]);

        $audioFile = $request->file('audio');
        $audioPath = $audioFile->store('songs', 'public');
        $coverPath = $request->file('cover')->store('covers', 'public');

        $getID3 = new \getID3;
        $fileInfo = $getID3->analyze($audioFile->getRealPath());

        $duration = isset($fileInfo['playtime_seconds'])
                ? (int)round($fileInfo['playtime_seconds'])
                : 0;

        $song = Song::create([
            'title' => $fields['title'],
            'plays' => 0,
            'length' => $duration,
            'stored_at' => 'app/public/' . $audioPath,
            'cover' => 'storage/public/' . $coverPath,
            'user_id' => $request->user()->id,
            'album_id' => null
        ]);

        return response()->json([
            'message' => 'Song uploaded successfully!',
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
        // $path = 'https://jcloud02.jedlik.eu/csongeto.csongor/' . $song->stored_at;
        // $path = storage_path('app/public/defaults/seeding/defsongs/default_song_17_stride.mp3');
        // return $path;
        // return response()->json(['path' => $path]);

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

        $song = Song::find($id);

        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        try {
            DB::transaction(function () use ($request, $song) {
                $song->increment('plays');

                $user = auth('sanctum')->user();

                ListeningHistoryItem::create([
                    'user_id'     => $user ? $user->id : null,
                    'song_id'     => $song->id,
                    'album_id'    => $song->album_id,
                    'playlist_id' => $request->input('playlist_id'),
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
    public function show($id)
    {
        $song = Song::with(['user:id,name', 'album:id,title'])->find($id);

        // $path = 'https://jcloud02.jedlik.eu/csongeto.csongor/' . $song->stored_at;
        // return response()->json(['path' => $path]);

        if (!$song) {
            return response()->json(['message' => 'Song not found!'], 404);
        }

        return response()->json($song);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $song = Song::find($id);

        if (!$song) {
            return response()->json(['message' => 'Song not found!'], 404);
        }

        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not your song!'], 403);
        }

        $fields = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'cover' => 'sometimes|required|image|mimes:jpg,jpeg,png|max:10000',
        ]);

        if ($request->has('title')) {
            $song->title = $fields['title'];
        }

        if ($request->hasFile('cover') && $song->album_id != null) {
            return response()->json(['message' => 'Cant update song cover, song is in an album!'], 400);
        }

        if ($request->hasFile('cover')) {

            $oldCoverPath = str_replace(['storage/public/', 'app/public/', 'public/', 'storage/'], '', $song->cover);

            if (!Str::startsWith($oldCoverPath, 'defaults')) {
                if (Storage::disk('public')->exists($oldCoverPath)) {
                    Storage::disk('public')->delete($oldCoverPath);
                }
            }

            $newCoverPath = $request->file('cover')->store('covers', 'public');

            $song->cover = 'storage/public/' . $newCoverPath;
        }

        $song->save();

        return response()->json([
            'message' => 'Song updated successfully!',
            'song' => $song
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $song = Song::findOrFail($id);

        if ($song->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not your song!'], 403);
        }

        $audioPath = str_replace(['storage/public/', 'app/public/', 'public/'], '', $song->stored_at);


        $coverPath = str_replace(['storage/public/', 'app/public/', 'public/', 'storage/'], '', $song->cover);


        if (!Str::startsWith($audioPath, 'defaults')) {
            if (Storage::disk('public')->exists($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
        }

        if (!Str::startsWith($coverPath, 'defaults')) {
            if (Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
        }

        $song->delete();

        return response()->json(['message' => 'Song deleted successfully!']);
    }

}
