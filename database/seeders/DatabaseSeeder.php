<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\PointsPerLevel;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $points = [
            83, 91, 102, 112, 124, 138, 151, 168, 185, 204, 226, 249, 274, 304, 335, 369, 408,
            450, 497, 548, 606, 667, 737, 814, 898, 990, 1094, 1207, 1332, 1470, 1623, 1791,
            1977, 2182, 2409, 2668, 2935, 3240, 3576, 3947, 4358, 4810, 5310, 5863, 6471, 7144,
            7887, 8707, 9612, 10612, 11715, 12934, 14278, 15764, 17404, 19214, 21212, 23420,
            25856, 28546, 31516, 34795, 38416, 42413, 46826, 51699, 57079, 63019, 69576, 76818,
            84812, 93638, 103383, 114143, 126022, 139138, 153619, 169608, 187260, 206750, 228269,
            252027, 278259, 307221, 339198, 374502, 413482, 456519, 504037, 556499, 614422, 678376,
            748985, 826944, 913019, 1008052, 1112977, 1228825
        ];

        for ($i = 0; $i < count($points); $i++) {
            PointsPerLevel::create([
                'level' => $i + 2,
                'points' => $points[$i],
            ]);
        }
    }
}
