<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(0, 10),
            'document_id' => rand(0, 10),
            'office_id' => rand(0, 10),
            'sender_name' => $this->faker->name,
            'message' => $this->faker->name,
            'status' => rand(0, 1),
        ];
    }
}
