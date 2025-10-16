<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Atualiza tabelas monetárias para usar brick/money:
     * - Adiciona coluna 'currency' (código ISO 4217)
     * - Atualiza precisão decimal de 2 para 3 casas decimais
     */
    public function up(): void
    {
        // Produtos
        Schema::table('products', function (Blueprint $table) {
            $table->string('currency', 3)->default('BRL')->after('price')->comment('Código ISO 4217 (BRL, USD, EUR)');
            $table->decimal('price', 15, 3)->nullable()->default(0.000)->change();
            $table->decimal('cost_price', 15, 3)->nullable()->default(0.000)->change();
        });

        // Serviços
        Schema::table('services', function (Blueprint $table) {
            $table->string('currency', 3)->default('BRL')->after('price')->comment('Código ISO 4217 (BRL, USD, EUR)');
            $table->decimal('price', 15, 3)->nullable()->default(0.000)->change();
            $table->decimal('cost_price', 15, 3)->nullable()->default(0.000)->change();
        });

        // Pacotes
        Schema::table('packages', function (Blueprint $table) {
            $table->string('currency', 3)->default('BRL')->after('price')->comment('Código ISO 4217 (BRL, USD, EUR)');
            $table->decimal('price', 15, 3)->nullable()->default(0.000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Produtos
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->decimal('price', 7, 2)->nullable()->default(0)->change();
            $table->decimal('cost_price', 7, 2)->nullable()->default(0)->change();
        });

        // Serviços
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->decimal('price', 7, 2)->nullable()->default(0)->change();
            $table->decimal('cost_price', 7, 2)->nullable()->default(0)->change();
        });

        // Pacotes
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->decimal('price', 7, 2)->nullable()->default(0)->change();
        });
    }
};
