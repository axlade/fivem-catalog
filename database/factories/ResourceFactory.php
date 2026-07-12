<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isFree = fake()->boolean(60);

        return [
            'user_id' => User::factory(),
            'title' => ucfirst(fake()->words(3, true)),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['scripts', 'mlos', 'eup', 'vehicles']),
            'framework' => fake()->randomElement(['esx', 'qb-core', 'standalone', 'ox']),
            'price' => $isFree ? 0 : fake()->randomFloat(2, 5, 60),
            'download_url' => $isFree ? 'https://github.com/example/resource' : null,
            'tebex_url' => $isFree ? null : 'https://example.tebex.io/package/123',
            'thumbnail_path' => 'thumbnails/placeholder.png',
            'status' => 'approved',
        ];
    }
}
