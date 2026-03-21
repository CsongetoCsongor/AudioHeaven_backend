<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Song;
use App\Models\Playlist;
use App\Models\QueueItem;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' => 'admin',
        ]);

        $testUser = User::factory()->create([
            'name' => 'Teszt Elek',
            'email' => 'teszt@example.com',
            'password' => Hash::make('password'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' =>'user',
        ]);

        $users = User::factory(5)->create();
        $allUsers = $users->concat([$testUser]);

        foreach ($allUsers as $user) {
        // 1. Minden felhasználó feltölt egy zenét, ami NINCS albumban
        Song::factory()->create([
            'user_id' => $user->id,
            'album_id' => null,
            'title' => 'Single - ' . fake()->words(2, true),
        ]);

        // 2. Minden felhasználó feltölt egy albumot
        $album = Album::factory()->create([
            'user_id' => $user->id,
            'title' => 'Album - ' . fake()->words(2, true),
        ]);

        // 3. Az albumhoz létrehozunk 3 zenét a kért borítóképpel
        Song::factory(3)->create([
            'user_id' => $user->id,
            'album_id' => $album->id,
            'cover' => 'storage/defaults/default_album_cover.png', // Fix borítókép az album zenéinek
        ]);

        // 4. Minden usernek csinálunk egy Playlistet is
        $playlist = Playlist::create([
            'title' => 'Kedvenceim - ' . $user->name,
            'user_id' => $user->id
        ]);

        // Rakjunk bele pár véletlen zenét a playlistbe (az összes dal közül válogatva)
        $randomSongs = Song::inRandomOrder()->limit(3)->get();
        $playlist->songs()->attach($randomSongs->pluck('id'));
    }

        // 6. A teszt usernek rakjunk valamit a Queue-jába is
        $queueSongs = Song::inRandomOrder()->limit(3)->get();
        foreach ($queueSongs as $index => $song) {
            QueueItem::create([
                'user_id' => $testUser->id,
                'song_id' => $song->id,
                'position' => $index + 1
            ]);
        }

    }
}
