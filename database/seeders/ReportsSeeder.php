<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    /**php artisan make:model Report
     * Run the database seeds.
     */
    public function run(): void
    {
        Report::factory()->count(10)->create();
    }
}
