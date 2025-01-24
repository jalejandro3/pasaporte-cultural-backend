<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = base_path('database/data/countries_cities.json');
        $data = json_decode(file_get_contents($jsonFile), true);

        foreach (array_chunk($data, 50) as $chunk) {
            foreach ($chunk as $countryData) {
                $country = Country::firstOrCreate(
                    ['iso_code' => $countryData['iso2']],
                    ['name' => $countryData['translations']['es'] ?? $countryData['name']]
                );

                foreach ($countryData['cities'] as $city) {
                    City::create([
                        'name' => $city['name'],
                        'country_id' => $country->id,
                    ]);
                }
            }
        }
    }
}
