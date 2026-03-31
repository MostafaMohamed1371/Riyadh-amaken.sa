<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::factory()->count(12)->create()->each(function (Event $event) {
            $event->tags()->sync(
                Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->all()
            );
        });
    }
}
