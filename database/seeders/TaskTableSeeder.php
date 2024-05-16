<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


/**
 * @property $faker
 */
class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory(10)->create();

        $parentIds = DB::table('tasks')->pluck('id')->toArray();

        foreach ($parentIds as $parentId) {
            Task::factory(1)->create([
                'parent_id' => $parentId,
            ]);
        }
    }
}
