<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_options', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {ar: "أحمر", en: "Red"}
            $table->enum('type', ['color', 'fabric_type', 'sleeve_type', 'dome_type']);
            $table->string('image')->nullable(); // صورة الخيار
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_options');
    }
};
