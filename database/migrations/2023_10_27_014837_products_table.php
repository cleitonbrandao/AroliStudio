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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->index();
            $table->decimal('buy_price', 8, 3);
            $table->decimal('sell_price', 8, 3);
            $table->float('weight', 8, 3);
            $table->integer('balance');
            $table->text('description');
        });
        DB::statement("ALTER TABLE products ADD bar_code INT(11) UNSIGNED ZEROFILL DEFAULT NULL AFTER name");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
