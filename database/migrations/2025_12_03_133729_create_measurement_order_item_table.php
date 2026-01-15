<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('measurement_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['order_item_id', 'measurement_id'], 'measurement_order_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_order_item');
    }
};
