<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['pending','in progress','completed']);
        $users = User::all();

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text(200),
            'due_date' => $this->faker->dateTimeBetween('+1day','+1month'),
            'status' => $status,
            'user_id' => $users->random()->id
        ];
    }
}
