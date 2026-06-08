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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('poster_image')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('original_price', 15, 2)->nullable();
            $table->decimal('promo_price', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_hot_deal')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
