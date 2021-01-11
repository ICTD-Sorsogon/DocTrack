<?php

namespace Database\Seeders;

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 2; $i++) {
            DB::table('users')->insert([
                'role_id' => '1',
                'username' => $faker->username,
                'password' => Hash::make('secret'),
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'suffix' => rand(0, 1) === 1 ? $faker->suffix : null,
                'gender' => rand(1, 2),
                'birthday' => $faker->date,
                'id_number' => $faker->ssn,
                'office_id' => rand(1, 3),
                'is_active' => true,
                'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'role_id' => '2',
                'username' => $faker->username,
                'password' => Hash::make('secret'),
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'suffix' => rand(0, 1) === 1 ? $faker->suffix : null,
                'gender' => rand(1, 2),
                'birthday' => $faker->date,
                'id_number' => $faker->ssn,
                'office_id' => rand(1, 3),
                'is_active' => true,
                'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            DB::table('users')->insert([
                'role_id' => '3',
                'username' => $faker->username,
                'password' => Hash::make('secret'),
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'suffix' => rand(0, 1) === 1 ? $faker->suffix : null,
                'gender' => rand(1, 2),
                'birthday' => $faker->date,
                'id_number' => $faker->ssn,
                'office_id' => rand(1, 3),
                'is_active' => true,
                'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
            ]);
        }
    }
}
