<?php

namespace Database\Factories;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistFactory extends Factory
{
    protected $model = Playlist::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'user_id' => User::factory(),
        ];
    }
}
