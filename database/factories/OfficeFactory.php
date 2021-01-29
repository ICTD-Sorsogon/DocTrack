<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $office_code = ['GO', 'VGO', 'PA', 'PGSO', 'PSWDO', 'PV', 'PPDO'];
        return [
            'name' => $this->faker->name,
            'address' => $this->faker->sentence,
            'office_code' => $office_code[rand(0, 6)],
            'contact_number' => rand(6, 5),
            'contact_email' => $this->faker->email,
        ];
    }
}
