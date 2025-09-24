<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('semesters')->insert([
            [
                'id' => 1,
                'name' => 'First',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Second',
                'created_at' => Carbon::create(2025, 5, 7, 22, 37, 1),
                'updated_at' => Carbon::create(2025, 5, 7, 22, 37, 1),
            ],
        ]);
    }
}

