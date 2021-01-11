<?php

namespace Database\Factories;

use App\Models\Personnel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Personnel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $id = 3;
        return [
            'id' => $id++,
            'name' => $this->faker->name,
            'office_id' => rand(1,36),
        ];
    }
}
