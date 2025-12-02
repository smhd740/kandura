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
        Schema::table('designs', function (Blueprint $table) {
            // حذف foreign key أولاً
            $table->dropForeign(['measurement_id']);

            // حذف العمود
            $table->dropColumn('measurement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            // لو بدك ترجع التعديل
            $table->foreignId('measurement_id')
                ->nullable()
                ->after('user_id')
                ->constrained('measurements')
                ->onDelete('cascade');
        });
    }
};
