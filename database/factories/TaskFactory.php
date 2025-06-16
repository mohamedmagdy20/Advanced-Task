<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dueDate = $this->faker->dateTimeBetween('now', '+1 month');
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'due_date' => $dueDate,
            'priority' => $this->faker->randomElement([
                Task::PRIORITY_HIGH,
                Task::PRIORITY_MEDIUM,
                Task::PRIORITY_LOW
            ]),
            'status'=>'pending',
            'user_id' => User::factory(),
        ];
    }
}
