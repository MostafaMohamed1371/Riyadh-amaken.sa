<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        Place::factory()->count(18)->create()->each(function (Place $place) {
            $place->tags()->sync(
                Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->all()
            );
        });
    }
}
