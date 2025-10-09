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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia')->required();
            $table->string('razao_social')->nullable();
            $table->bigInteger('cnpj')->nullable();
            $table->string('inscricao_estatual', 20)->nullable();
            $table->string('bussines_email')->nullable();
            $table->timestamps();
        });
        DB::statement(
            'ALTER TABLE costumers MODIFY COLUMN cpf BIGINT(14) UNSIGNED ZEROFILL AFTER person_id'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
