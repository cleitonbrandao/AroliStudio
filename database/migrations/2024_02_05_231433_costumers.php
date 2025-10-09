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
        Schema::create('costumers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')
                ->constrained(table: 'peoples', indexName: 'costumer_people_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('cpf')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->unique(['person_id']);
        });
        DB::statement(
            'ALTER TABLE costumers MODIFY COLUMN cpf BIGINT(11) UNSIGNED ZEROFILL AFTER person_id'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costumers');
    }
};
