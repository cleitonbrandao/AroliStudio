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
        Schema::table('teams', function (Blueprint $table) {
            // Adicionar slug (se não existir)
            if (!Schema::hasColumn('teams', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
            
            // Adicionar campos de limites e controle
            $table->integer('max_users')->default(10)->after('personal_team');
            $table->integer('current_users')->default(0)->after('max_users');
            
            // Adicionar campos de plano e status
            $table->string('plan_type')->default('free')->after('current_users');
            $table->boolean('is_active')->default(true)->after('plan_type');
            $table->timestamp('trial_ends_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Remover colunas na ordem inversa
            $table->dropColumn([
                'trial_ends_at',
                'is_active',
                'plan_type',
                'current_users',
                'max_users',
            ]);
            
            // Remover slug se foi criado
            if (Schema::hasColumn('teams', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
