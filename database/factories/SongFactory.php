<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'plays' => fake()->numberBetween(0, 1000),
            'length' => fake()->numberBetween(120, 300),
            'stored_at' => 'app/public/defaults/default_song_' . fake()->numberBetween(1, 3) . '.mp3',
            'cover' => 'storage/defaults/default_song_cover.png',
            'user_id' => \App\Models\User::factory(),
            'album_id' => null,
        ];
    }
}
