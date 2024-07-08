<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US', 'Google_Code' => '2840', 'ISO_CODE' => 'USA'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'Google_Code' => '20339', 'ISO_CODE' => 'GBR'],
            ['name' => 'India', 'code'=> 'IN', 'Google_code' => '2356','ISO_CODE'=> 'IND']
        ];
        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }
        $this->command->info('Countries seeded successfully.');
    }
}
