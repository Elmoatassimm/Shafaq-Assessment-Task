<?php
// database/factories/TaskFactory.php
namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Associate with a user
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => 'pending', // Default status
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'priority' => null, // Default to null
        ];
    }
}