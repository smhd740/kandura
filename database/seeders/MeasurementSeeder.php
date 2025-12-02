<?php

namespace Database\Seeders;

use App\Models\Measurement;
use App\Models\User;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الأحجام الثابتة
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        // جيب أول user من النوع 'user' (أو أنشئ واحد للتجربة)
        $user = User::where('role', 'user')->first();

        if (!$user) {
            // إذا ما في user، أنشئ واحد للتجربة
            $user = User::create([
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'is_active' => true,
            ]);
        }

        // أنشئ قياس واحد لكل size للـ user التجريبي
        foreach ($sizes as $size) {
            Measurement::firstOrCreate([
                'user_id' => $user->id,
                'size' => $size,
            ]);
        }

        $this->command->info('✅ Measurements seeded successfully!');
    }
}
