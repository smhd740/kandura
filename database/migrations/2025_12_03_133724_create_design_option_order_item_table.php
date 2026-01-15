<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_option_order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['order_item_id', 'design_option_id'], 'design_option_order_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_option_order_item');
    }
};
