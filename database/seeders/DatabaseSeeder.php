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

    protected $getID3;

    public function run(): void
    {
        $this->getID3 = new \getID3;

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'profile_picture' => 'storage/public/defaults/default_profile_picture.png',
            'role' => 'admin',
        ]);


    //Teszt Elek
        $user = User::create([
            'name' => 'Teszt Elek',
            'email' => 'teszt@example.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_1.png',
            'role' => 'user',
        ]);

        $singles = [
            ['id' => 1, 'title_fn' => 'ominous', 'title' => 'Ominous', 'ext' => 'mp3', 'img_ext' => 'jpg'],
            ['id' => 2, 'title_fn' => 'outside', 'title' => 'Outside', 'ext' => 'mp3', 'img_ext' => 'jpg'],
            ['id' => 3, 'title_fn' => 'veil', 'title' => 'Veil', 'ext' => 'mp3', 'img_ext' => 'png'],
            ['id' => 15, 'title_fn' => 'stayvigilant', 'title' => 'Stay Vigilant', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        $this->seedSongs($singles, $user->id);

        $albumCover = "storage/public/defaults/seeding/defalbumcovers/default_album_cover_16_17_18.jpg";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Jumpstyle',
            'album_cover' => $albumCover,
        ]);

        $albumSongs = [
            ['id' => 16, 'title_fn' => 'ugrasstilus', 'title' => 'ugrasstilus', 'ext' => 'mp3'],
            ['id' => 17, 'title_fn' => 'stride', 'title' => 'stride', 'ext' => 'mp3'],
            ['id' => 18, 'title_fn' => 'altered', 'title' => 'altered', 'ext' => 'mp3'],
        ];

        $this->seedSongs($albumSongs, $user->id, $album->id, $albumCover);
    //The React Reckoning
        $user = User::create([
            'name' => 'The React Reckoning',
            'email' => 'thereactreckoning@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_2.jpg',
            'role' => 'user',
        ]);


        $albumCover = "storage/public/defaults/seeding/defalbumcovers/default_album_cover_4_5.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Static Sites / Dynamic Hate',
            'album_cover' => $albumCover,
        ]);

        $albumSongs = [
            ['id' => 4, 'title_fn' => 'kingofthestack', 'title' => 'King Of The Stack', 'ext' => 'mp3'],
            ['id' => 5, 'title_fn' => 'riseofnextjs', 'title' => 'Rise Of Next.js', 'ext' => 'mp3'],
        ];


        $this->seedSongs($albumSongs, $user->id, $album->id, $albumCover);


    //ThugCode
        $user = User::create([
            'name' => 'ThugCode',
            'email' => 'thugcode@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_3.png',
            'role' => 'user',
        ]);

        $singles = [
            ['id' => 19, 'title_fn' => 'nopivotssqldisstrack', 'title' => 'No Pivots (SQL Disstrack)', 'ext' => 'mp3', 'img_ext' => 'png'],
            ['id' => 21, 'title_fn' => '3tmongodbcompassdisstrack', 'title' => '3T (MongoDB Compass Disstrack)', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        $this->seedSongs($singles, $user->id);

        $albumCover = "storage/public/defaults/seeding/defalbumcovers/default_album_cover_6_7_8_9_10_11.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Codedwellers',
            'album_cover' => $albumCover,
        ]);

        $albumSongs = [
            ['id' => 6, 'title_fn' => 'codeignite', 'title' => 'Code Ignite', 'ext' => 'mp3'],
            ['id' => 7, 'title_fn' => 'laravellifestyle', 'title' => 'Laravel Lifestyle', 'ext' => 'mp3'],
            ['id' => 8, 'title_fn' => 'greenbaranthem', 'title' => 'Green Bar Anthem', 'ext' => 'mp3'],
            ['id' => 9, 'title_fn' => 'greenbaranthem2', 'title' => 'Green Bar Anthem 2', 'ext' => 'mp3'],
            ['id' => 10, 'title_fn' => 'restrage', 'title' => 'Rest Rage', 'ext' => 'mp3'],
            ['id' => 11, 'title_fn' => 'mauimadness', 'title' => 'Maui Madness', 'ext' => 'mp3'],
        ];

        $this->seedSongs($albumSongs, $user->id, $album->id, $albumCover);

    //Cyber Lovers
        $user = User::create([
            'name' => 'Cyber Lovers',
            'email' => 'cyberlovers@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_4.png',
            'role' => 'user',
        ]);

        $singles = [
            ['id' => 12, 'title_fn' => 'objectorientedlove', 'title' => 'Object Oriented Love', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        $this->seedSongs($singles, $user->id);

        $albumCover = "storage/public/defaults/seeding/defalbumcovers/default_album_cover_13_14.png";

        $album = Album::create([
            'user_id' => $user->id,
            'title' => 'Next Love',
            'album_cover' => $albumCover,
        ]);

        // 4. Album zenéi (ID: 13 és 14)
        $albumSongs = [
            ['id' => 13, 'title_fn' => 'hotreloadheart', 'title' => 'Hot Reload Heart', 'ext' => 'mp3'],
            ['id' => 14, 'title_fn' => 'thecomponenttomyheart', 'title' => 'The Component To My Heart', 'ext' => 'mp3'],
        ];

        $this->seedSongs($albumSongs, $user->id, $album->id, $albumCover);

    //Lil Sql
        $user = User::create([
            'name' => 'Lil Sql',
            'email' => 'lilsql@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_5.png',
            'role' => 'user',
        ]);

        $singles = [
            ['id' => 20, 'title_fn' => 'silentdropnosqldisstrack', 'title' => 'Silent Drop (NoSQL Disstrack)', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        $this->seedSongs($singles, $user->id);

    //mongoca$h
    $user = User::create([
            'name' => 'mongoca$h',
            'email' => 'mongocash@audioheaven.com',
            'password' => Hash::make('password123'),
            'profile_picture' => 'storage/public/defaults/seeding/defprofilepictures/default_profile_picture_6.png',
            'role' => 'user',
        ]);

        $singles = [
            ['id' => 22, 'title_fn' => 'mongowizardstudio3tdisstrack', 'title' => 'Mongo Wizard (Studio3T Disstrack)', 'ext' => 'mp3', 'img_ext' => 'png'],
        ];

        $this->seedSongs($singles, $user->id);

    }

    protected function getAudioDuration(string $path): int
    {
        $fullPath = storage_path("app/{$path}");

        if (!file_exists($fullPath)) {
            Log::warning("Seeder hiba: A fájl nem található: {$fullPath}");
            return 0;
        }

        $fileInfo = $this->getID3->analyze($fullPath);

        return isset($fileInfo['playtime_seconds'])
            ? (int)round($fileInfo['playtime_seconds'])
            : 0;
    }

    protected function seedSongs(array $songs, int $userId, ?int $albumId = null, ?string $albumCover = null): void
    {

        foreach ($songs as $s) {
            $relativeSongPath = "defaults/seeding/defsongs/default_song_{$s['id']}_{$s['title_fn']}.{$s['ext']}";

            Song::create([
                'user_id'   => $userId,
                'title'     => $s['title'],
                'plays'     => rand(10, 500),
                'length'    => $this->getAudioDuration("public/{$relativeSongPath}"),
                'stored_at' => "app/public/{$relativeSongPath}",
                'cover'     => $albumId == null ? "storage/public/defaults/seeding/defsongcovers/default_song_cover_{$s['id']}.{$s['img_ext']}" : $albumCover,
                'album_id'  => $albumId,
            ]);
        }
    }
}
