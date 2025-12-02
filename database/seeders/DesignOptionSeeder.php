<?php

namespace Database\Seeders;

use App\Models\DesignOption;
use Illuminate\Database\Seeder;

class DesignOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            // Colors
            [
                'name' => ['ar' => 'أبيض', 'en' => 'White'],
                'type' => 'color',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'أسود', 'en' => 'Black'],
                'type' => 'color',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'أزرق', 'en' => 'Blue'],
                'type' => 'color',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'بيج', 'en' => 'Beige'],
                'type' => 'color',
                'is_active' => true,
            ],

            // Fabric Types
            [
                'name' => ['ar' => 'قطن', 'en' => 'Cotton'],
                'type' => 'fabric_type',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'حرير', 'en' => 'Silk'],
                'type' => 'fabric_type',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'كتان', 'en' => 'Linen'],
                'type' => 'fabric_type',
                'is_active' => true,
            ],

            // Sleeve Types
            [
                'name' => ['ar' => 'كم طويل', 'en' => 'Long Sleeve'],
                'type' => 'sleeve_type',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'كم قصير', 'en' => 'Short Sleeve'],
                'type' => 'sleeve_type',
                'is_active' => true,
            ],

            // Dome Types
            [
                'name' => ['ar' => 'قبة مستديرة', 'en' => 'Round Dome'],
                'type' => 'dome_type',
                'is_active' => true,
            ],
            [
                'name' => ['ar' => 'قبة مربعة', 'en' => 'Square Dome'],
                'type' => 'dome_type',
                'is_active' => true,
            ],
        ];

        foreach ($options as $option) {
            DesignOption::firstOrCreate(
                ['name' => $option['name'], 'type' => $option['type']],
                $option
            );
        }

        $this->command->info('✅ Design Options seeded successfully!');
    }
}
