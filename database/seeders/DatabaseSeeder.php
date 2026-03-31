<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@riyadhamaken.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            BannerSeeder::class,
            PlaceSeeder::class,
            EventSeeder::class,
        ]);
    }
}
