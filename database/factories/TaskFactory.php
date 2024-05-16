<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->text(20),
            'description' => $this->faker->text(20),
            'status' => $this->faker->randomElement([
                'todo',
                'done',
            ]),
            'priority' => $this->faker->numberBetween(1, 5),
            'user_id' => $this->faker->numberBetween(1, 3),
        ];
    }
}
