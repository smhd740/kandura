<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_option_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained('designs')->onDelete('cascade');
            $table->foreignId('design_option_id')->constrained('design_options')->onDelete('cascade');
            $table->text('custom_value')->nullable(); // للقيم المخصصة (optional)
            $table->timestamps();

            $table->index('design_id');
            $table->index('design_option_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_option_selections');
    }
};
