<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'short_description' => fake()->sentence(),
            'full_description' => fake()->paragraph(3),
            'address' => fake()->streetAddress(),
            'latitude' => fake()->latitude(24.5, 25.2),
            'longitude' => fake()->longitude(46.2, 47.2),
            'city' => 'Riyadh',
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'instagram' => fake()->url(),
            'image' => fake()->imageUrl(),
            'gallery' => [fake()->imageUrl(), fake()->imageUrl()],
            'rating' => fake()->randomFloat(1, 3, 5),
            'price_range' => fake()->randomElement(['$', '$$', '$$$', '$$$$']),
            'working_hours' => [
                'sun-thu' => '10:00 - 23:00',
                'fri-sat' => '14:00 - 01:00',
            ],
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
            'meta_title' => $name.' in Riyadh',
            'meta_description' => fake()->sentence(),
        ];
    }
}
