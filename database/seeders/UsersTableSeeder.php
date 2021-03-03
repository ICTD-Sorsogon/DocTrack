<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class UsersTableSeeder extends Seeder
{
    static $images = [
        'images/zoro.png',
        'images/bigmom.jpg',
        'images/blackbeard.jpg',
        'images/kaido.jpg',
        'images/shanks.jpg',
        'images/luffy.png',
        'images/nami.png',
        'images/robin.jpg',
        'images/chopper.jpg'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $path = public_path('images');
        // User::factory()->create(['username' => 'admin', 'role_id' => 1, 'office_id' => '37', 'avatar' => 'images/zoro.png']);
        // User::factory()->create(['username' => 'GO', 'role_id' => 3, 'office_id' => '1', 'avatar' => 'images/bigmom.jpg']);
        // User::factory()->create(['username' => 'VGO', 'role_id' => 2, 'office_id' => '2', 'avatar' => 'images/blackbeard.jpg']);
        // User::factory()->create(['username' => 'MSP', 'role_id' => 2, 'office_id' => '3', 'avatar' => 'images/kaido.jpg']);
        // User::factory()->create(['username' => 'SSP', 'role_id' => 2, 'office_id' => '4', 'avatar' => 'images/shanks.jpg']);
        // User::factory()->create(['username' => 'HRMO', 'role_id' => 2, 'office_id' => '17', 'avatar' => 'images/luffy.png']);
        foreach(Office::all() as $office) {
            if ($office->office_code =='DO') User::factory()->create(['username' => $office->office_code, 'role_id' => 1, 'office_id' => $office->id, 'avatar' => 'images/luffy.png']);
            else if ($office->office_code =='GO') User::factory()->create(['username' => $office->office_code, 'role_id' => 3, 'office_id' => $office->id, 'avatar' => 'images/zoro.png']);
            else User::factory()->create(['username' => $office->office_code, 'role_id' => 2, 'office_id' => $office->id, 'avatar' => self::$images[rand(0, 8)]]);
        }

    }
}
