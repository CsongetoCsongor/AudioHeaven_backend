<?php

use App\Models\User;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

test('index: kilistázza a felhasználókat és szűrhető név alapján', function () {

    User::factory()->create(['name' => 'Teszt Elek']);
    User::factory()->create(['name' => 'Kovács János']);


    $response = $this->getJson('/api/users?search=Elek');


    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonFragment(['name' => 'Teszt Elek']);
});

test('random: megadott számú véletlenszerű felhasználót ad vissza', function () {
    User::factory()->count(5)->create();

    $response = $this->getJson('/api/users/random?count=3');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('show: visszaadja a felhasználót a hozzá tartozó dalokkal és albumokkal', function () {
    $user = User::factory()->create();

    // Létrehozunk neki egy dalt és egy albumot
    Song::factory()->create(['user_id' => $user->id]);
    Album::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'user',
                 'songs' => [['user']],
                 'albums' => [['user']]
             ]);
});

test('me: visszaadja a jelenleg bejelentkezett felhasználót', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
                     ->getJson('/api/me');

    $response->assertStatus(200)
             ->assertJsonPath('id', $user->id)
             ->assertJsonPath('email', $user->email);
});
