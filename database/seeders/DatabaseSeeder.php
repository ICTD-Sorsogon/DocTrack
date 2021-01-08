<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            OfficesTableSeeder::class,
            DivisionsTableSeeder::class,
            UnitsTableSeeder::class,
            SectorsTableSeeder::class,
            UsersTableSeeder::class,
            DocumentTypesSeeder::class,
            DocumentsTableSeeder::class,
        ]);
    }
}
