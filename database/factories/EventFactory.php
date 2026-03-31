<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $startDate = fake()->dateTimeBetween('now', '+3 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'short_description' => fake()->sentence(),
            'full_description' => fake()->paragraph(3),
            'location' => fake()->streetAddress().', Riyadh',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->boolean(40) ? fake()->dateTimeBetween($startDate, '+4 months')->format('Y-m-d') : null,
            'start_time' => '18:00',
            'end_time' => '22:00',
            'ticket_price' => fake()->randomFloat(2, 0, 500),
            'booking_url' => fake()->url(),
            'image' => fake()->imageUrl(),
            'gallery' => [fake()->imageUrl()],
            'organizer' => fake()->company(),
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
            'meta_title' => $title,
            'meta_description' => fake()->sentence(),
        ];
    }
}
