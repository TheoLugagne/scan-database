<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        Gender::create([
            'name' => 'Action',
        ]);
        Gender::create([
            'name' => 'Adventure',
        ]);
        Gender::create([
            'name' => 'Comedy',
        ]);
        Gender::create([
            'name' => 'Drama',
        ]);
        Gender::create([
            'name' => 'Fantasy',
        ]);
        Gender::create([
            'name' => 'Horror',
        ]);
        Gender::create([
            'name' => 'Mystery',
        ]);
        Gender::create([
            'name' => 'Romance',
        ]);
        Gender::create([
            'name' => 'Sci-Fi',
        ]);
        Gender::create([
            'name' => 'Thriller',
        ]);
        Gender::create([
            'name' => 'Martial Arts',
        ]);
    }
}
