<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlaConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SlaConfig::updateOrCreate(['priority' => 'high'], ['hours' => 4]);
        \App\Models\SlaConfig::updateOrCreate(['priority' => 'medium'], ['hours' => 24]);
        \App\Models\SlaConfig::updateOrCreate(['priority' => 'low'], ['hours' => 72]);
    }
}
