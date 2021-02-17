<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $pics = ['images/brook.jfif', 'images/chopper.jpg', 'images/franky.jpg',
                'images/jinbei.jfif', 'images/nami.png', 'images/robin.jpg',
                'images/sanji.png', 'images/usopp.png'];

        return [
            'role_id' => rand(1, 3),
            'username' => $this->faker->username,
            'password' => Hash::make('secret'),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'suffix' => rand(0, 1) === 1 ? $this->faker->suffix : null,
            'gender' => rand(1, 2),
            'birthday' => $this->faker->date,
            'id_number' => $this->faker->ssn,
            'office_id' => rand(3, 19),
            'avatar' => $pics[rand(0,7)],
            'is_active' => true,
            'created_at' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null)
        ];
    }
}
