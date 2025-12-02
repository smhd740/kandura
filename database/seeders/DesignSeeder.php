<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\DesignImage;
use App\Models\DesignOptionSelection;
use App\Models\DesignOption;
use App\Models\Measurement;
use App\Models\User;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جيب user تجريبي
        $user = User::where('role', 'user')->first();

        if (!$user) {
            $this->command->warn('⚠️  No user found. Run MeasurementSeeder first.');
            return;
        }

        // جيب قياسات
        $measurements = Measurement::where('user_id', $user->id)->get();

        if ($measurements->isEmpty()) {
            $this->command->warn('⚠️  No measurements found. Run MeasurementSeeder first.');
            return;
        }

        // جيب خيارات التصميم
        $colorOption = DesignOption::where('type', 'color')->first();
        $fabricOption = DesignOption::where('type', 'fabric_type')->first();

        // أنشئ 5 تصاميم تجريبية
        for ($i = 1; $i <= 5; $i++) {
            $design = Design::create([
                'user_id' => $user->id,
                'measurement_id' => $measurements->random()->id,
                'name' => [
                    'ar' => "تصميم كندورة {$i}",
                    'en' => "Kandura Design {$i}"
                ],
                'description' => [
                    'ar' => "وصف التصميم رقم {$i} - تصميم عصري وأنيق",
                    'en' => "Design description {$i} - Modern and elegant design"
                ],
                'price' => rand(100, 500),
                'is_active' => true,
            ]);

            // أضف صورة تجريبية
            DesignImage::create([
                'design_id' => $design->id,
                'image_path' => 'designs/sample-' . $i . '.jpg', // مسار تجريبي
                'is_primary' => true,
                'order' => 1,
            ]);

            // أضف خيارات تصميم
            if ($colorOption) {
                DesignOptionSelection::create([
                    'design_id' => $design->id,
                    'design_option_id' => $colorOption->id,
                ]);
            }

            if ($fabricOption) {
                DesignOptionSelection::create([
                    'design_id' => $design->id,
                    'design_option_id' => $fabricOption->id,
                ]);
            }
        }

        $this->command->info('✅ Designs seeded successfully!');
    }
}
