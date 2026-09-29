<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\scan>
 */
class ScanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availableChapters = fake()->optional()->numberBetween(0, 300);

        return [
            'create_date' => now(),
            'last_update' => now()->nullable(),
            'title' => fake()->unique()->title(),
            'summary' => fake()->optional()->text(),
            'current_chapter' => fake()->randomFloat(1, 100),
            'cover_image' => fake()->optional()->imageUrl(),
            'link_to_scan' => fake()->optional()->url(),
            'available_chapters' => $availableChapters,
            'available_chapters_updated_at' => $availableChapters === null ? null : now(),
        ];
    }
}
