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
        Schema::table('enterprise', function (Blueprint $table) {
            $table->foreign('person_id')
                ->references('id')
                ->on('enterprise')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprise', function (Blueprint $table) {
            $table->dropForeign('enterprise_person_id_foreign');
        });
    }
};
