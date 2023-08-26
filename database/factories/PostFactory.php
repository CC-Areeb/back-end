<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'excerpt' => fake()->paragraph(),
            'body' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->slug,
            'author' => $this->faker->name,
            'category' => $this->faker->word,
            'tags' => $this->faker->words(3, true),
            'image' => $this->faker->imageUrl(800, 600, 'nature'),
        ];
    }
}
