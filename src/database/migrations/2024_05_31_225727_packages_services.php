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
        Schema::create('packages_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained(table: 'services', indexName: 'package_service_service_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('package_id')
                ->constrained(table: 'packages', indexName: 'package_service_package_id')
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
        Schema::dropIfExists('packages_services');
    }
};
