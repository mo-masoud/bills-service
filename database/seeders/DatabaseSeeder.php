<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PaymentMethod::updateOrCreate([
            'name' => 'Binance',
        ], [
            'name' => 'Binance',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Binance_logo.svg/1280px-Binance_logo.svg.png',
            'is_active' => true,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
