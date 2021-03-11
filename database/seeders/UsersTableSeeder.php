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
        foreach(Office::all() as $office) {
            if ($office->office_code =='DO') User::factory()->create(['username' => $office->office_code, 'role_id' => 1, 'office_id' => $office->id, 'avatar' => 'images/luffy.png']);
            else if ($office->office_code =='GO') User::factory()->create(['username' => $office->office_code, 'role_id' => 3, 'office_id' => $office->id, 'avatar' => 'images/zoro.png']);
            else User::factory()->create(['username' => $office->office_code, 'role_id' => 2, 'office_id' => $office->id, 'avatar' => self::$images[rand(0, 8)]]);
        }

    }
}
