<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // حذف دسته‌ای که محصول دارد مجاز نیست (restrict)
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('description', 500)->nullable();
            $table->string('brand', 100)->index();
            $table->string('origin', 100)->nullable();
            $table->string('status', 10)->default('in'); // in | low | out

            // قیمت‌ها به تومان
            $table->unsignedBigInteger('price_factory_cash')->default(0);
            $table->unsignedBigInteger('price_factory_installment')->default(0);
            $table->string('factory_installment_terms', 255)->nullable();
            $table->unsignedBigInteger('price_baloochi_cash')->default(0);
            $table->unsignedBigInteger('price_baloochi_installment')->default(0);
            $table->string('baloochi_installment_terms', 255)->nullable();

            // رسانه: فایل آپلودی یا لینک خارجی
            $table->string('image_path')->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_url', 2048)->nullable();

            $table->text('extra_info')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
