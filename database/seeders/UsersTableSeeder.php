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
        User::factory()->create(['username' => 'GO', 'role_id' => 3, 'office_id' => '1', 'avatar' => 'images/bigmom.jpg']);
        User::factory()->create(['username' => 'VGO', 'role_id' => 2, 'office_id' => '2', 'avatar' => 'images/blackbeard.jpg']);
        User::factory()->create(['username' => 'MSP', 'role_id' => 2, 'office_id' => '3', 'avatar' => 'images/kaido.jpg']);
        User::factory()->create(['username' => 'SSP', 'role_id' => 2, 'office_id' => '4', 'avatar' => 'images/shanks.jpg']);
        User::factory()->create(['username' => 'HRMO', 'role_id' => 2, 'office_id' => '17', 'avatar' => 'images/luffy.png']);
        User::factory()->create(['username' => 'PBOO', 'role_id' => 2, 'office_id' => '9', 'avatar' => 'images/shanks.jpg']);
        User::factory()->create(['username' => 'PAA', 'role_id' => 2, 'office_id' => '7', 'avatar' => 'images/kaido.jpg']);
        User::factory()->create(['username' => 'PTO', 'role_id' => 2, 'office_id' => '5', 'avatar' => 'images/chopper.jpg']);
        User::factory()->create(['username' => 'admin', 'role_id' => 1, 'office_id' => '37', 'avatar' => 'images/zoro.png']);
        User::factory()->times(14)->create(['role_id'=> 2]);
    }
}
