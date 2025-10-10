<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->integer('max_users')->default(5)->after('personal_team');
            $table->integer('current_users')->default(1)->after('max_users');
            $table->string('plan_type')->default('free')->after('current_users');
            $table->boolean('is_active')->default(true)->after('plan_type');
            $table->timestamp('trial_ends_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'max_users',
                'current_users',
                'plan_type',
                'is_active',
                'trial_ends_at',
            ]);
        });
    }
};
