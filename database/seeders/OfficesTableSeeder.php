<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offices = [
            [
                'name' => 'Office of the Governor',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'GO',
                'contact_number' => '09613551323',
                'contact_email' => 'governor@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Vice Governor',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'VGO',
                'contact_number' => '09104111426',
                'contact_email' => 'vicegovernor@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Secretary to the Sangguniang Panlalawigan',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'SP',
                'contact_number' => '09193620918',
                'contact_email' => 'spsec@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Treasurer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PTO',
                'contact_number' => '09104111426',
                'contact_email' => 'pto@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Assessor',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PAO',
                'contact_number' => '09104111426',
                'contact_email' => 'pao@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Accountant',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PAA',
                'contact_number' => '09104111426',
                'contact_email' => 'paa@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Engineer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PEO',
                'contact_number' => '09104111426',
                'contact_email' => 'peo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Budget Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PBOO',
                'contact_number' => '09104111426',
                'contact_email' => 'pboo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Planning and Development Office',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PPDO',
                'contact_number' => '09104111426',
                'contact_email' => 'ppdo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Administrator',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PA',
                'contact_number' => '09104111426',
                'contact_email' => 'pa@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Legal Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PLO',
                'contact_number' => '09104111426',
                'contact_email' => 'plo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Social Welfare and Development Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PSWDO',
                'contact_number' => '09104111426',
                'contact_email' => 'pswdo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial General Services Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PGSO',
                'contact_number' => '09104111426',
                'contact_email' => 'pgso@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Agriculturist',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PAG',
                'contact_number' => '09104111426',
                'contact_email' => 'pag@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Veterenarian',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PV',
                'contact_number' => '09104111426',
                'contact_email' => 'pv@sorsogon.gov.ph',
            ],
            [
                'name' => 'Provincial Human Resource Management Office',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'HRMO',
                'contact_number' => '09104111426',
                'contact_email' => 'hrmo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Sorsogon Provincial Disaster Risk Reduction Management Office',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'SPDRRMO',
                'contact_number' => '09104111426',
                'contact_email' => 'spdrrmo@sorsogon.gov.ph',
            ],

        ];
        DB::table('offices')->insert($offices);
    }
}
