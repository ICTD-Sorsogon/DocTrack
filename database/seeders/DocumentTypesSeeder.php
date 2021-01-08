<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Executive Order'],
            ['name' => 'Provincial Ordinance'],
            ['name' => 'Letter'],
            ['name' => 'Purchase Order'],
            ['name' => 'Salary'],
            ['name' => 'Budget'],
            ['name' => 'Reports'],
            ['name' => 'Draft'],
            ['name' => 'Others'],
        ];
        DB::table('document_types')->insert($types);
    }
}
