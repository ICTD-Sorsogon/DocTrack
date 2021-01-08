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
                'name' => 'Office of the Members of the Sangguniang Panlalawigan',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'MSP',
                'contact_number' => '09193620918',
                'contact_email' => 'spsec@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Secretary to the Sangguniang Panlalawigan',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'SSP',
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
            [
                'name' => 'Office of the Provincial Health Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PHO',
                'contact_number' => '09104111426',
                'contact_email' => 'pho@sorsogon.gov.ph',
            ],
            [
                'name' => 'Dr. Fernando B. Duran, Sr. Memorial Hospital',
                'address' => 'Macabog, Sorsogon City, Sorsogon, Sorsogon Diversion Rd, Sorsogon City, Sorsogon',
                'office_code' => 'DFBDSMH',
                'contact_number' => '(056)2111032',
                'contact_email' => 'dfbdsmh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Pantaleon G. Gotladera Memorial Hospital',
                'address' => 'Bulan, Sorsogon',
                'office_code' => 'PGGMH',
                'contact_number' => '',
                'contact_email' => 'pggmh@sorsogon.gov.ph',
            ],
            [
                'name' => 'V.L. Peralta Memorial Hospital',
                'address' => 'Castilla, Sorsogon',
                'office_code' => 'VLPMH',
                'contact_number' => '',
                'contact_email' => 'vlpmh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Donsol District Hospital',
                'address' => 'Donsol, Sorsogon',
                'office_code' => 'DDH',
                'contact_number' => '',
                'contact_email' => 'ddh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Salvador R. Encinas District Hospital',
                'address' => 'Bonifacio St, Gubat, Sorsogon',
                'office_code' => 'SREDH',
                'contact_number' => '',
                'contact_email' => 'sredh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Irosin District Hospital',
                'address' => 'Irosin, Sorsogon',
                'office_code' => 'IDH',
                'contact_number' => '',
                'contact_email' => 'idh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Magallanes Medicare Hospital',
                'address' => 'Magallanes, Sorsogon',
                'office_code' => 'MMH',
                'contact_number' => '',
                'contact_email' => 'mmh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Pto. Diaz Municipal Hospital',
                'address' => 'Prieto Diaz, Sorsogon',
                'office_code' => 'PDMH',
                'contact_number' => '',
                'contact_email' => 'pdmh@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Natural Resources and Environment Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PNREO',
                'contact_number' => '',
                'contact_email' => 'pnreo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Natural Resources and Environment Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PNREO',
                'contact_number' => '',
                'contact_email' => 'pnreo@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Cooperative Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PCO',
                'contact_number' => '',
                'contact_email' => 'pco@sorsogon.gov.ph',
            ],
            [
                'name' => 'Provincial Library and Information Center',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'LIC',
                'contact_number' => '',
                'contact_email' => 'lic@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Tourism Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PTO',
                'contact_number' => '',
                'contact_email' => 'pto@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Internal Control Officer',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PICO',
                'contact_number' => '',
                'contact_email' => 'pico@sorsogon.gov.ph',
            ],
            [
                'name' => 'Public Employment Service Office',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'PESO',
                'contact_number' => '',
                'contact_email' => 'peso@sorsogon.gov.ph',
            ],
            [
                'name' => 'Office of the Provincial Warden',
                'address' => 'Capitol Compound, Burabod, Sorsogon City, Sorsogon',
                'office_code' => 'OPW',
                'contact_number' => '',
                'contact_email' => 'opw@sorsogon.gov.ph',
            ],
            [
                'name' => 'Sorsogon Community College',
                'address' => 'Kasanggayahan Compound, Arellano Street, Salog, Sorsogon City, Sorsogon',
                'office_code' => 'OPW',
                'contact_number' => '(056) 255 0390',
                'contact_email' => 'ssc@sorsogonstatecollege.edu.ph',
            ],
        ];
        DB::table('offices')->insert($offices);
    }
}
