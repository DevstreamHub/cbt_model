<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('levels')->insert([
            ['id' => 1, 'name' => '100Level'],
            ['id' => 2, 'name' => '200Level'],
            ['id' => 3, 'name' => '300Level'],
        ]);
    }
}
