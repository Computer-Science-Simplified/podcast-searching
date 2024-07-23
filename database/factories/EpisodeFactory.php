<?php

namespace Database\Factories;

use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'podcast_id' => Podcast::factory(),
            'title' => $this->faker->words(5, true),
            'summary' => $this->faker->realText(rand(100, 500)),
            'content' => $this->faker->realText(50_000),
            'audio_file_path' => storage_path('app/sample.mp3'),
        ];
    }
}
