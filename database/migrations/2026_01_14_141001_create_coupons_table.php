<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // الكود الفريد
            $table->enum('discount_type', ['percentage', 'fixed']); // نسبة أو رقم ثابت
            $table->decimal('amount', 10, 2); // القيمة: 5 = 5% أو 5 ليرات
            $table->integer('max_usage')->unsigned(); // عدد المرات المسموح استخدامه
            $table->integer('used_count')->unsigned()->default(0); // عدد المرات المستخدمة
            $table->timestamp('starts_at')->nullable(); // من إيمتى صالح (اختياري)
            $table->timestamp('expires_at'); // لإيمتى صالح
            $table->decimal('min_order_amount', 10, 2)->nullable(); // الحد الأدنى لسعر الطلب
            $table->boolean('is_active')->default(true);
            $table->boolean('is_user_specific')->default(false); // هل خاص بمستخدمين معينين
            $table->text('description')->nullable(); // وصف الكوبون
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index(['starts_at', 'expires_at']);
            $table->index('discount_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
