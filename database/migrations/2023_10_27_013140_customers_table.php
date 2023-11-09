<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id');
            $table->string('email')->unique();
            $table->integer('cell-phone')->unique();
            $table->integer('phone');
        });
        DB::statement("ALTER TABLE customers ADD cpf INT(11) UNSIGNED ZEROFILL NOT NULL AFTER person_id");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
