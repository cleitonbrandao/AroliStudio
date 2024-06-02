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
        Schema::create('groups_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_origin_id')
                ->constrained(table: 'packages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('package_id')
                ->constrained(table: 'packages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups_packages');

    }
};
