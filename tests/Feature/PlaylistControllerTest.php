<?php

use App\Models\User;
use App\Models\Song;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index: returns all playlists belonging to the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Create 2 playlists for our user and 1 for someone else
    Playlist::factory()->count(2)->create(['user_id' => $user->id]);
    Playlist::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)
                     ->getJson('/api/playlists');

    $response->assertStatus(200)
             ->assertJsonCount(2);
});

test('show: returns a specific playlist with its songs, creators, and albums', function () {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create(['user_id' => $user->id]);
    $song = Song::factory()->create();

    // Attach song to playlist
    $playlist->songs()->attach($song->id);

    // Itt a kulcs: actingAs($user) hozzáadása!
    $response = $this->actingAs($user)
                     ->getJson("/api/playlists/{$playlist->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id', 'title', 'user', 'songs' => [
                     '*' => ['id', 'title', 'user', 'album']
                 ]
             ])
             ->assertJsonPath('title', $playlist->title);
});

test('addSong: successfully adds a song to a playlist', function () {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create(['user_id' => $user->id]);
    $song = Song::factory()->create();

    $response = $this->actingAs($user)
                     ->postJson("/api/playlists/{$playlist->id}/songs/{$song->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['message' => 'Song successfully added to playlist!']);

    // Database check
    $this->assertTrue($playlist->songs()->where('song_id', $song->id)->exists());
});

// test('addSong: returns 409 conflict if song is already in the playlist', function () {
//     $user = User::factory()->create();
//     $playlist = Playlist::factory()->create(['user_id' => $user->id]);
//     $song = Song::factory()->create();

//     // Add it once first
//     $playlist->songs()->attach($song->id);

//     // Try adding it again
//     $response = $this->actingAs($user)
//                      ->postJson("/api/playlists/{$playlist->id}/songs/{$song->id}");

//     $response->assertStatus(409)
//              ->assertJson(['message' => 'Song already added to playlist!']);
// });

// test('addSong: returns 403 if the user tries to modify someone else\'s playlist', function () {
//     $owner = User::factory()->create();
//     $hacker = User::factory()->create();
//     $playlist = Playlist::factory()->create(['user_id' => $owner->id]);
//     $song = Song::factory()->create();

//     $response = $this->actingAs($hacker)
//                      ->postJson("/api/playlists/{$playlist->id}/songs/{$song->id}");

//     $response->assertStatus(403)
//              ->assertJson(['message' => 'Not your playlist!']);
// });
