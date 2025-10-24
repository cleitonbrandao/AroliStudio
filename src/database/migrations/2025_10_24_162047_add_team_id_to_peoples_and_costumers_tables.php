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
        // Adicionar team_id à tabela peoples
        Schema::table('peoples', function (Blueprint $table) {
            $table->foreignId('team_id')
                ->nullable()
                ->after('id')
                ->constrained('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
                
            $table->index('team_id');
        });

        // Adicionar team_id à tabela costumers
        Schema::table('costumers', function (Blueprint $table) {
            $table->foreignId('team_id')
                ->nullable()
                ->after('id')
                ->constrained('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
                
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peoples', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropIndex(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::table('costumers', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropIndex(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
