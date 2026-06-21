<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('status');
            }

            if (!Schema::hasColumn('events', 'premium_price')) {
                $table->decimal('premium_price', 15, 2)->nullable()->after('is_premium');
            }
        });
    }

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