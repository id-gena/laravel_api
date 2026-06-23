<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(5)
            ->has(Task::factory()->count(10)->withRandomPriority()->withRandomDueDate())
            ->create();
    }
}
