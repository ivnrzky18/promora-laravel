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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('poster_image');
            }

            if (!Schema::hasColumn('events', 'premium_price')) {
                $table->decimal('premium_price', 12, 2)->nullable()->after('is_premium');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'premium_price')) {
                $table->dropColumn('premium_price');
            }

            if (Schema::hasColumn('events', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
        });
    }
};