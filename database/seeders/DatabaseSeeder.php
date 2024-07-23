<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Podcast;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Episode::factory(20)->create();
    }
}
