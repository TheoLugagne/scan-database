<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        Genre::create([
            'name' => 'Action',
        ]);
        Genre::create([
            'name' => 'Adventure',
        ]);
        Genre::create([
            'name' => 'Comedy',
        ]);
        Genre::create([
            'name' => 'Drama',
        ]);
        Genre::create([
            'name' => 'Fantasy',
        ]);
        Genre::create([
            'name' => 'Horror',
        ]);
        Genre::create([
            'name' => 'Mystery',
        ]);
        Genre::create([
            'name' => 'Romance',
        ]);
        Genre::create([
            'name' => 'Sci-Fi',
        ]);
        Genre::create([
            'name' => 'Thriller',
        ]);
        Genre::create([
            'name' => 'Martial Arts',
        ]);
    }
}
