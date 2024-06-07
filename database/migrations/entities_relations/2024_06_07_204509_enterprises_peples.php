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
        Schema::create('enterprises_peoples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')
                ->constrained(table: 'enterprise', indexName: 'enterprise_people_enterprise_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('people_id')
                ->constrained(table: 'peoples', indexName: 'enterprise_people_people_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises_peoples');
    }
};
