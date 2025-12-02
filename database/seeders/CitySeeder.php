<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['ar' => 'دمشق', 'en' => 'Damascus'],
            ['ar' => 'حلب', 'en' => 'Aleppo'],
            ['ar' => 'حمص', 'en' => 'Homs'],
            ['ar' => 'حماة', 'en' => 'Hama'],
            ['ar' => 'اللاذقية', 'en' => 'Latakia'],
            ['ar' => 'طرطوس', 'en' => 'Tartus'],
            ['ar' => 'درعا', 'en' => 'Daraa'],
            ['ar' => 'السويداء', 'en' => 'As-Suwayda'],
            ['ar' => 'القنيطرة', 'en' => 'Quneitra'],
            ['ar' => 'إدلب', 'en' => 'Idlib'],
            ['ar' => 'الرقة', 'en' => 'Ar-Raqqah'],
            ['ar' => 'دير الزور', 'en' => 'Deir ez-Zor'],
            ['ar' => 'الحسكة', 'en' => 'Al-Hasakah'],
            ['ar' => 'ريف دمشق', 'en' => 'Rif Dimashq'],
        ];

        foreach ($cities as $city) {
            City::create([
                'name' => [
                    'ar' => $city['ar'],
                    'en' => $city['en'],
                ],
                'is_active' => true,
            ]);
        }
    }
}
