<?php

use App\Models\User;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index: lists all songs and can filter by title', function () {
    // Prepare: Create an album and songs with specific titles
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    Song::factory()->create(['title' => 'Midnight City', 'user_id' => $user->id, 'album_id' => $album->id]);
    Song::factory()->create(['title' => 'Solar Echoes', 'user_id' => $user->id]);

    // Action: Search for "Midnight"
    $response = $this->getJson('/api/songs?search=Midnight');

    // Assert
    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonFragment(['title' => 'Midnight City'])
             ->assertJsonStructure([
                 '*' => ['user' => ['id', 'name'], 'album' => ['id', 'title']]
             ]);
});

test('listByUser: returns all songs belonging to a specific user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Create 3 songs for the target user and 1 for someone else
    Song::factory()->count(3)->create(['user_id' => $user->id]);
    Song::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/users/{$user->id}/songs");

    $response->assertStatus(200)
             ->assertJsonCount(3)
             ->assertJsonStructure([
                 '*' => ['user' => ['id', 'name']]
             ]);
});

// test('listByUser: returns 404 if the user does not exist', function () {
//     $response = $this->getJson('/api/users/999/songs');

//     $response->assertStatus(404)
//              ->assertJson(['message' => 'User not found!']);
// });

test('getNewSongs: returns the most recently created songs', function () {
    // 1. Create an old song (10 days ago)
    Song::factory()->create([
        'title' => 'Old Song',
        'created_at' => now()->subDays(10)
    ]);

    // 2. Create a "new" song (5 minutes ago)
    Song::factory()->create([
        'title' => 'The Second Newest',
        'created_at' => now()->subMinutes(5)
    ]);

    // 3. Create the ABSOLUTE newest song (now)
    $newestSong = Song::factory()->create([
        'title' => 'The Absolute Newest',
        'created_at' => now()
    ]);

    $response = $this->getJson('/api/songs/new?count=2');

    $response->assertStatus(200)
             ->assertJsonCount(2);

    // The first item in the JSON (index 0) must be the absolute newest one
    $this->assertEquals('The Absolute Newest', $response->json()[0]['title']);
    $this->assertEquals('The Second Newest', $response->json()[1]['title']);
});

test('random: returns a specified number of songs in random order', function () {
    Song::factory()->count(10)->create();

    $response = $this->getJson('/api/songs/random?count=5');

    $response->assertStatus(200)
             ->assertJsonCount(5)
             ->assertJsonStructure([
                 '*' => ['user', 'album']
             ]);
});
