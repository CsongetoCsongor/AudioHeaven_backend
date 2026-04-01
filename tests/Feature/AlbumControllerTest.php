<?php

use App\Models\User;
use App\Models\Album;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index: lists all albums and can search by title or username', function () {
    // Prepare: Create two users and their albums
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Smith']);

    Album::factory()->create(['title' => 'The Great Revival', 'user_id' => $user1->id]);
    Album::factory()->create(['title' => 'Smith Vibes', 'user_id' => $user2->id]);

    // Search by Album Title
    $responseByTitle = $this->getJson('/api/albums?search=Revival');
    $responseByTitle->assertStatus(200)
                    ->assertJsonCount(1)
                    ->assertJsonFragment(['title' => 'The Great Revival']);

    // Search by Artist Name
    $responseByArtist = $this->getJson('/api/albums?search=Smith');
    $responseByArtist->assertStatus(200)
                     ->assertJsonCount(1)
                     ->assertJsonFragment(['title' => 'Smith Vibes']);
});

test('listByUser: returns all albums belonging to a specific user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Album::factory()->count(2)->create(['user_id' => $user->id]);
    Album::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/users/{$user->id}/albums");

    $response->assertStatus(200)
             ->assertJsonCount(2)
             ->assertJsonStructure([
                 '*' => ['user', 'songs']
             ]);
});

test('random: returns a specified number of random albums', function () {
    Album::factory()->count(10)->create();

    $response = $this->getJson('/api/albums/random?count=4');

    $response->assertStatus(200)
             ->assertJsonCount(4)
             ->assertJsonStructure([
                 '*' => ['id', 'title', 'user']
             ]);
});

test('show: returns a specific album with its songs and user details', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    // Create songs associated with this album
    Song::factory()->count(3)->create([
        'album_id' => $album->id,
        'user_id' => $user->id
    ]);

    $response = $this->getJson("/api/albums/{$album->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id', 'title', 'album_cover', 'user', 'songs' => [
                     '*' => ['id', 'title', 'user']
                 ]
             ])
             ->assertJsonPath('title', $album->title)
             ->assertJsonCount(3, 'songs');
});

// test('show: returns 404 if the album does not exist', function () {
//     $response = $this->getJson('/api/albums/999');

//     $response->assertStatus(404)
//              ->assertJson(['message' => 'Album not found!']);
// });
