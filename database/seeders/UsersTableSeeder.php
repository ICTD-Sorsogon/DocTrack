<?php

namespace Database\Seeders;

use App\Models\User;
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
    public function run()
    {
        User::factory()->create(['username' => 'GO', 'role_id' => 2, 'office_id' => '1']);
        User::factory()->create(['username' => 'VGO', 'role_id' => 2, 'office_id' => '2']);
        User::factory()->create(['username' => 'MSP', 'role_id' => 2, 'office_id' => '3']);
        User::factory()->create(['username' => 'SSP', 'role_id' => 2, 'office_id' => '4']);
        User::factory()->create(['username' => 'HRMO', 'role_id' => 2, 'office_id' => '17']);
        User::factory()->create(['username' => 'admin', 'role_id' => 1, 'office_id' => '37']);
        User::factory()->times(2)->create(['role_id'=>1]);
        User::factory()->times(10)->create(['role_id'=>2]);
        User::factory()->times(2)->create(['role_id'=>3]);
    }
}
