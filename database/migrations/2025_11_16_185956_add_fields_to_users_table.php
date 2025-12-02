<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('role', ['guest', 'user', 'admin', 'super_admin'])->default('user')->after('password');
            $table->string('profile_image')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('profile_image');
            $table->softDeletes(); // deleted_at

            // Indexes للأداء
            $table->index('phone');
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'profile_image',
                'is_active',
                'deleted_at'
            ]);
        });
    }
};
