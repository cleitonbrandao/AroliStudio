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
        Schema::create('enterprise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id');
            $table->string('fantasy_name')->index();
            $table->string('social_reason')->index();
            $table->integer('incricao_estadual');
            $table->string('inscricao_municipal');

        });
        DB::statement("ALTER TABLE enterprise ADD cnpj BIGINT UNSIGNED ZEROFILL NOT NULL AFTER social_reason");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise');
    }
};
