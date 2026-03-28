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
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Admin User',
    //         'email' => 'admin@example.com',
    //         'password' => Hash::make('password'),
    //         'profile_picture' => 'storage/defaults/default_profile_picture.png',
    //         'role' => 'admin',
    //     ]);

    //     $testUser = User::factory()->create([
    //         'name' => 'Teszt Elek',
    //         'email' => 'teszt@example.com',
    //         'password' => Hash::make('password'),
    //         'profile_picture' => 'storage/defaults/default_profile_picture.png',
    //         'role' =>'user',
    //     ]);

    //     $users = User::factory(5)->create();
    //     $allUsers = $users->concat([$testUser]);

    //     foreach ($allUsers as $user) {
    //     // 1. Minden felhasználó feltölt egy zenét, ami NINCS albumban
    //     Song::factory()->create([
    //         'user_id' => $user->id,
    //         'album_id' => null,
    //         'title' => 'Single - ' . fake()->words(2, true),
    //     ]);

    //     // 2. Minden felhasználó feltölt egy albumot
    //     $album = Album::factory()->create([
    //         'user_id' => $user->id,
    //         'title' => 'Album - ' . fake()->words(2, true),
    //     ]);

    //     // 3. Az albumhoz létrehozunk 3 zenét a kért borítóképpel
    //     Song::factory(3)->create([
    //         'user_id' => $user->id,
    //         'album_id' => $album->id,
    //         'cover' => 'storage/defaults/default_album_cover.png', // Fix borítókép az album zenéinek
    //     ]);

    //     // 4. Minden usernek csinálunk egy Playlistet is
    //     $playlist = Playlist::create([
    //         'title' => 'Kedvenceim - ' . $user->name,
    //         'user_id' => $user->id
    //     ]);

    //     // Rakjunk bele pár véletlen zenét a playlistbe (az összes dal közül válogatva)
    //     $randomSongs = Song::inRandomOrder()->limit(3)->get();
    //     $playlist->songs()->attach($randomSongs->pluck('id'));
    // }

    //     // 6. A teszt usernek rakjunk valamit a Queue-jába is
    //     $queueSongs = Song::inRandomOrder()->limit(3)->get();
    //     foreach ($queueSongs as $index => $song) {
    //         QueueItem::create([
    //             'user_id' => $testUser->id,
    //             'song_id' => $song->id,
    //             'position' => $index + 1
    //         ]);
    //     }

    // }

    // 1. Teszt Elek
        $user = User::create([
            'name' => 'Teszt Elek',
            'email' => 'teszt@example.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' => 'user',
        ]);

        // --- SINGLE ZENÉK ---
        $singles = [
            ['id' => 1, 'title_fn' => 'Ominous', 'title' => 'Ominous', 'ext' => 'mp3', 'img_ext' => 'jpg'],
            ['id' => 2, 'title_fn' => 'Outside', 'title' => 'Outside', 'ext' => 'mp3', 'img_ext' => 'jpg'],
            ['id' => 3, 'title_fn' => 'Veil', 'title' => 'Veil', 'ext' => 'mp3', 'img_ext' => 'png'],
            ['id' => 15, 'title_fn' => 'Stayvigilant', 'title' => 'Stay Vigilant', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        foreach ($singles as $s) {
            Song::create([
                'user_id' => $user->id,
                'title' => $s['title'],
                'plays' => rand(10, 500),
                'length' => rand(120, 240),
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$s['id']}_{$s['title_fn']}.{$s['ext']}",
                'cover' => "storage/defaults/seeding/defsongcovers/default_song_cover_{$s['id']}.{$s['img_ext']}",
                'album_id' => null,
            ]);
        }

        // --- ALBUM LÉTREHOZÁSA ---
        $albumCover = "storage/defaults/seeding/defalbumcovers/default_album_cover_16_17_18.jpg";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Jumpstyle',
            'album_cover' => $albumCover,
        ]);

        // --- ALBUM ZENÉI ---
        $albumSongs = [
            ['id' => 16, 'title_fn' => 'Ugrasstilus', 'title' => 'ugrasstilus', 'ext' => 'mp3'],
            ['id' => 17, 'title_fn' => 'Stride', 'title' => 'stride', 'ext' => 'mp3'],
            ['id' => 18, 'title_fn' => 'Bloodvessel', 'title' => 'blood vessel', 'ext' => 'mp3'],
        ];

        foreach ($albumSongs as $as) {
            Song::create([
                'user_id' => $user->id,
                'title' => $as['title'],
                'plays' => rand(100, 1000),
                'length' => rand(150, 300),
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$as['id']}_{$as['title_fn']}.{$as['ext']}",
                'cover' => $albumCover,
                'album_id' => $album->id,
            ]);
        }
    //The React Reckoning
        $user = User::create([
            'name' => 'The React Reckoning',
            'email' => 'thereactreckoning@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' => 'user',
        ]);

        // 2. Album létrehozása
        $albumCover = "storage/defaults/seeding/defalbumcovers/default_album_cover_4_5.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Static Sites / Dynamic Hate',
            'album_cover' => $albumCover,
        ]);

        // 3. Album zenéi (ID: 4 és 5)
        $albumSongs = [
            ['id' => 4, 'title_fn' => 'KingOfTheStack', 'title' => 'King Of The Stack', 'ext' => 'mp3'],
            ['id' => 5, 'title_fn' => 'RiseOfNextjs', 'title' => 'Rise Of Next.js', 'ext' => 'mp3'],
        ];


        foreach ($albumSongs as $as) {
            Song::create([
                'user_id' => $user->id,
                'album_id' => $album->id,
                'title' => $as['title'],
                'plays' => rand(50, 1000),
                'length' => rand(180, 300),
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$as['id']}_{$as['title_fn']}.{$as['ext']}",
                'cover' => $albumCover,
            ]);
        }

    //ThugCode
        $user = User::create([
            'name' => 'ThugCode',
            'email' => 'thugcode@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' => 'user',
        ]);

        // 2. Album létrehozása
        $albumCover = "storage/defaults/seeding/defalbumcovers/default_album_cover_6_7_8_9_10_11.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Codedwellers',
            'album_cover' => $albumCover,
        ]);

        // 3. Album zenéi (ID: 6-tól 11-ig)
        // A fájlnevek alapján: id_cim.mp3
        $albumSongs = [
            ['id' => 6, 'title_fn' => 'Codeignite', 'title' => 'Code Ignite', 'ext' => 'mp3'],
            ['id' => 7, 'title_fn' => 'Laravellifestyle', 'title' => 'Laravel Lifestyle', 'ext' => 'mp3'],
            ['id' => 8, 'title_fn' => 'Greenbaranthem', 'title' => 'Green Bar Anthem', 'ext' => 'mp3'],
            ['id' => 9, 'title_fn' => 'Greenbaranthem2', 'title' => 'Green Bar Anthem 2', 'ext' => 'mp3'],
            ['id' => 10, 'title_fn' => 'Restrage', 'title' => 'Rest Rage', 'ext' => 'mp3'],
            ['id' => 11, 'title_fn' => 'Mauimadness', 'title' => 'Maui Madness', 'ext' => 'mp3'],
        ];

        foreach ($albumSongs as $as) {
            Song::create([
                'user_id' => $user->id,
                'album_id' => $album->id,
                'title' => $as['title'], // Szebb megjelenítés (pl. Rest Rage)
                'plays' => rand(100, 5000),
                'length' => rand(120, 300),
                // Elérési út felépítése: default_song_6_codeignite.mp3
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$as['id']}_{$as['title_fn']}.{$as['ext']}",
                'cover' => $albumCover,
            ]);
        }

    // Cyber Lovers
        $user = User::create([
            'name' => 'Cyber Lovers',
            'email' => 'cyberlovers@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/defaults/default_profile_picture.png',
            'role' => 'user',
        ]);

        // 2. Single zene létrehozása (Object Oriented Love)
        // A képed alapján: default_song_12_objectorientedlove.mp3 és default_song_cover_12.png
        Song::create([
            'user_id' => $user->id,
            'album_id' => null,
            'title' => 'Object Oriented Love',
            'plays' => rand(100, 2000),
            'length' => rand(180, 240),
            'stored_at' => "app/public/defaults/seeding/defsongs/default_song_12_objectorientedlove.mp3",
            'cover' => "storage/defaults/seeding/defsongcovers/default_song_cover_12.png",
        ]);


        $singles = [
            ['id' => 12, 'title_fn' => 'Objectorientedlove', 'title' => 'Object Oriented Love', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        foreach ($singles as $s) {
            Song::create([
                'user_id' => $user->id,
                'title' => $s['title'],
                'plays' => rand(10, 500),
                'length' => rand(120, 240),
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$s['id']}_{$s['title_fn']}.{$s['ext']}",
                'cover' => "storage/defaults/seeding/defsongcovers/default_song_cover_{$s['id']}.{$s['img_ext']}",
                'album_id' => null,
            ]);
        }

        // 3. Album létrehozása (Next Love)
        // A képed alapján: default_album_cover_13_14.png
        $albumCover = "storage/defaults/seeding/defalbumcovers/default_album_cover_13_14.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Next Love',
            'album_cover' => $albumCover,
        ]);

        // 4. Album zenéi (ID: 13 és 14)
        $albumSongs = [
            ['id' => 13, 'title_fn' => 'Hotreloadheart', 'title' => 'Hot Reload Heart', 'ext' => 'mp3'],
            ['id' => 14, 'title_fn' => 'Thecomponenttomyheart', 'title' => 'The Component To My Heart', 'ext' => 'mp3'],
        ];

        foreach ($albumSongs as $as) {
            Song::create([
                'user_id' => $user->id,
                'album_id' => $album->id,
                'title' => $as['title'],
                'plays' => rand(500, 3000),
                'length' => rand(200, 350),
                'stored_at' => "app/public/defaults/seeding/defsongs/default_song_{$as['id']}_{$as['title_fn']}.{$as['ext']}",
                'cover' => $albumCover,
            ]);
        }

    }
}
